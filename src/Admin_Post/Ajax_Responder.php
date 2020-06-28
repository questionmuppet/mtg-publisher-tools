<?php
/**
 * Ajax_Responder
 * 
 * Sends the result of an admin post event via AJAX
 */

namespace Mtgtools\Admin_Post;
use \Mtgtools\Admin_Post\Interfaces\Admin_Post_Responder;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Ajax_Responder implements Admin_Post_Responder
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
     * Get WP prefix for hook action
     */
    public function get_wp_prefix() : string
    {
        return 'wp_ajax';
    }

}   // End of class