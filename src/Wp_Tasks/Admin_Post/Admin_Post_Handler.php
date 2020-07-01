<?php
/**
 * Admin_Post_Handler
 * 
 * Registers an action to handle POST requests to the WordPress admin
 */

namespace Mtgtools\Wp_Tasks\Admin_Post;
use Mtgtools\Abstracts\Data;
use Mtgtools\Wp_Tasks\Admin_Post\Interfaces\Admin_Post_Responder;
use Mtgtools\Exceptions\Admin_Post as Exceptions;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Admin_Post_Handler extends Data
{
    /**
     * Required properties
     */
    protected $required = array(
        'action',
        'callback',
    );

    /**
     * Default properties
     */
    protected $defaults = array(
        'capability' => 'manage_options',
        'user_args'  => [],
        'nopriv'     => false,
    );

    /**
     * Responder object
     */
    private $responder;

    /**
     * Constructor
     */
    public function __construct( array $params, Admin_Post_Responder $responder )
    {
        $this->responder = $responder;
        parent::__construct( $params );
    }

    /**
     * -----------------
     *   P R O C E S S
     * -----------------
     */

    /**
     * Add WP hooks
     */
    public function add_hooks() : void
    {
        foreach ( $this->get_wp_keys() as $hook )
        {
            add_action( $hook, array( $this, 'process_action' ) );
        }
    }

    /**
     * Process action
     */
    public function process_action() : void
    {
        try
        {
            $this->authorize();
            $result = $this->execute();
            $this->responder->handle_success( $result );
        }
        catch ( Exceptions\PostHandlerException $e )
        {
            $this->responder->handle_error( $e );
        }
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
        if ( !$this->is_permitted() )
        {
            throw new Exceptions\AuthorizationException( "You do not have permission for the requested action." );
        }
        if ( !$this->verify_nonce() )
        {
            throw new Exceptions\AuthorizationException( "The specified nonce for the requested action is invalid or expired." );
        }
    }
    
    /**
     * Check user permissions
     */
    private function is_permitted() : bool
    {
        return current_user_can( $this->get_prop('capability') );
    }

    /**
     * Verify nonce
     */
    private function verify_nonce() : bool
    {
        return wp_verify_nonce( $_POST['_wpnonce'] ?? '', $this->get_action() );
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
        return $this->get_prop( 'callback' );
    }

    /**
     * Get user arguments for the callback action
     */
    private function get_user_args() : array
    {
        $args = [];
        foreach ( $this->get_prop( 'user_args' ) as $key )
        {
            $args[ $key ] = sanitize_text_field( $_POST[ $key ] ?? '' );
        }
        return $args;
    }
    
    /**
     * ---------------
     *   A C T I O N
     * ---------------
     */

    /**
     * Get keys for WP hooks
     */
    private function get_wp_keys() : array
    {
        return array_filter([
            $this->get_hook_key(),
            $this->allows_public_access() ? $this->get_hook_key( 'nopriv' ) : '',
        ]);
    }
    
    /**
     * Get a concatenated hook string
     * 
     * @param string $interfix  Token to insert between WordPress prefix and consumer-provided action
     */
    private function get_hook_key( string $interfix = '' ) : string
    {
        $parts = array_filter([
            $this->responder->get_wp_prefix(),
            $interfix,
            $this->get_action(),
        ]);
        return implode( '_', $parts );
    }

    /**
     * Check if logged out users can access
     */
    private function allows_public_access() : bool
    {
        return boolval( $this->get_prop( 'nopriv' ) );
    }
    
    /**
     * Get action keyname
     */
    public function get_action() : string
    {
        return $this->get_prop( 'action' );
    }

}   // End of class