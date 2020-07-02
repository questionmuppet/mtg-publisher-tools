<?php
/**
 * Admin_Request_Responder
 * 
 * Handles the response produced by a request to the WordPress admin
 */

namespace Mtgtools\Wp_Tasks\Admin_Post;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

interface Admin_Request_Responder
{
    /**
     * Handle success state of admin request
     */
    public function handle_success( array $result ) : void;

    /**
     * Handle error state of admin request
     */
    public function handle_error( \Exception $e ) : void;

    /**
     * Whether or not response method uses AJAX
     */
    public function is_ajax() : bool;

}   // End of interface