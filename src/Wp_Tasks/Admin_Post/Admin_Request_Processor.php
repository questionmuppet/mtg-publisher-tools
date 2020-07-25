<?php
/**
 * Admin_Request_Processor
 * 
 * Authorizes requests to the WordPress admin and returns the result
 */

namespace Mtgtools\Wp_Tasks\Admin_Post;
use Mtgtools\Abstracts\Data;
use Mtgtools\Exceptions\Admin_Post as Exceptions;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Admin_Request_Processor extends Data
{
    /**
     * Default properties
     */
    protected $defaults = array(
        'nonce_key'     => '_wpnonce',
        'nonce_context' => null,
        'capability'    => 'manage_options',
        'callback'      => null,
        'user_args'     => [],
    );

    /**
     * -----------------
     *   P R O C E S S
     * -----------------
     */

    /**
     * Process request and return result
     * 
     * @throws PostHandlerException
     */
    public function process_request( array $params = [] ) : array
    {
        $this->set_props( $params );
        $this->authorize();
        return $this->execute();
    }

    /**
     * ---------------------
     *   A U T H O R I Z E
     * ---------------------
     */
    
    /**
     * Authorize action
     * 
     * @throws PostHandlerException
     */
    private function authorize() : void
    {
        if ( !$this->verify_nonce() )
        {
            $e = new Exceptions\AuthorizationException( "This link has expired. Please reload the page and try again." );
            $e->add_http_status( 401, 'Unauthorized' );
            throw $e;
        }
        if ( !$this->is_permitted() )
        {
            $e = new Exceptions\AuthorizationException( "You do not have permission for the requested action." );
            $e->add_http_status( 403, 'Forbidden' );
            throw $e;
        }
    }
    
    /**
     * Check user permissions
     */
    private function is_permitted() : bool
    {
        return current_user_can( $this->get_capability() );
    }

    /**
     * Get WP capability required for action
     */
    private function get_capability() : string
    {
        return $this->get_prop( 'capability' );
    }

    /**
     * Verify nonce
     */
    private function verify_nonce() : bool
    {
        return wp_verify_nonce( $this->get_nonce(), $this->get_nonce_context() );
    }
    
    /**
     * -------------
     *   N O N C E
     * -------------
     */

    /**
     * Get nonce
     */
    private function get_nonce() : string
    {
        $nonce = $_REQUEST[ $this->get_nonce_key() ] ?? '';
        return is_scalar( $nonce )
            ? strval( $nonce )
            : '';
    }

    /**
     * Get nonce key
     */
    private function get_nonce_key() : string
    {
        return $this->get_prop( 'nonce_key' );
    }

    /**
     * Get nonce context
     */
    private function get_nonce_context() : string
    {
        $context = $this->get_prop( 'nonce_context' );
        if ( !is_string( $context ) || empty( $context ) )
        {
            throw new \InvalidArgumentException(
                sprintf(
                    "Missing or invalid nonce context provided to %s. You must provide a non-empty context string to process an admin-post request.",
                    get_called_class()
                )
            );
        }
        return $context;
    }

    /**
     * -----------------
     *   E X E C U T E
     * -----------------
     */

    /**
     * Execute action and return result
     * 
     * @return array Result of the action callback function
     */
    private function execute() : array
    {
        $result = call_user_func( $this->get_callback(), $this->get_user_args() );
        if ( !is_array( $result ) )
        {
            throw new \UnexpectedValueException(
                sprintf(
                    "A user-provided callback function to %s returned an invalid type. Your post-handler callback must return an array.",
                    get_called_class()
                )
            );
        }
        return $result;
    }
    
    /**
     * Get callback function
     */
    private function get_callback() : callable
    {
        $callback = $this->get_prop( 'callback' );
        if ( !is_callable( $callback ) )
        {
            throw new \InvalidArgumentException(
                sprintf(
                    "Missing or invalid callback function provided to %s. You must provide a post-handler callback function to process an admin-post request.",
                    get_called_class()
                )
            );
        }
        return $callback;
    }

    /**
     * Get user arguments for the callback action
     */
    private function get_user_args() : array
    {
        $args = [];
        foreach ( $this->get_prop( 'user_args' ) as $key )
        {
            $args[ $key ] = sanitize_text_field( $_REQUEST[ $key ] ?? '' );
        }
        return $args;
    }

}   // End of class