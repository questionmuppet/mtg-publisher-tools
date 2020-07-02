<?php
/**
 * Ajax_Responder
 * 
 * Sends the result of an admin-post event via AJAX
 */

namespace Mtgtools\Wp_Tasks\Admin_Post;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Ajax_Responder implements Admin_Request_Responder
{
    /**
     * Handle success state
     */
    public function handle_success( array $result ) : void
    {
        wp_send_json_success( $result );
    }

    /**
     * Handle error state
     */
    public function handle_error( \Exception $e ) : void
    {
        wp_send_json_error([
            'error' => $e->getMessage()
        ]);
    }

    /**
     * Check response method
     */
    public function is_ajax() : bool
    {
        return true;
    }

}   // End of class