<?php
/**
 * Ajax_Handler
 * 
 * Registers a handler for an ajax request to the WordPress admin
 */

namespace Mtgtools\Wp_Tasks\Admin_Post;
use Mtgtools\Exceptions\Admin_Post\PostHandlerException;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Ajax_Handler extends Admin_Post_Handler
{
    /**
     * Default properties
     */
    protected $defaults = array(
        'is_ajax' => true,
    );

    /**
     * Handle success state
     */
    protected function handle_success( array $result ) : void
    {
        wp_send_json_success( $result );
    }

    /**
     * Handle error state
     */
    protected function handle_error( PostHandlerException $e ) : void
    {
        wp_send_json_error([
            'error' => $e->getMessage()
        ]);
    }

}   // End of class