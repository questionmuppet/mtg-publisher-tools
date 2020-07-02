<?php
/**
 * Admin_Post_Handler
 * 
 * Registers and handles WordPress admin requests through admin-post.php
 */

namespace Mtgtools\Wp_Tasks\Admin_Post;
use Mtgtools\Abstracts\Data;
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
    protected $abstract_defaults = array(
        'capability' => 'manage_options',
        'user_args'  => [],
        'nopriv'     => false,
    );

    /**
     * Request processor
     */
    private $processor;

    /**
     * Request responder
     */
    private $responder;

    /**
     * Constructor
     */
    public function __construct( Admin_Request_Processor $processor, Admin_Request_Responder $responder, array $props = [] )
    {
        $this->processor = $processor;
        $this->responder = $responder;
        parent::__construct( $props );
    }

    /**
     * -----------------
     *   H A N D L E R
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
            $result = $this->processor->process_request([
                'nonce_context' => $this->get_action(),
                'capability'    => $this->get_capability(),
                'callback'      => $this->get_callback(),
                'user_args'     => $this->get_user_args(),
            ]);
            $this->responder->handle_success( $result );
        }
        catch ( Exceptions\PostHandlerException $e )
        {
            $this->responder->handle_error( $e );
        }
    }
    
    /**
     * -------------------------
     *   A C T I O N   H O O K
     * -------------------------
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
            $this->get_wp_prefix(),
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

    /**
     * Get WP prefix for hook action
     */
    private function get_wp_prefix() : string
    {
        return $this->responder->is_ajax() ? 'wp_ajax' : 'admin_post';
    }

    /**
     * -----------------------------
     *   R E Q U E S T   P R O P S
     * -----------------------------
     */

    /**
     * Get capability required to execute action
     */
    private function get_capability() : string
    {
        return $this->get_prop( 'capability' );
    }

    /**
     * Get callback function to process request
     */
    private function get_callback() : callable
    {
        return $this->get_prop( 'callback' );
    }

    /**
     * Get user args for callback function
     */
    private function get_user_args() : array
    {
        return $this->get_prop( 'user_args' );
    }

}   // End of class