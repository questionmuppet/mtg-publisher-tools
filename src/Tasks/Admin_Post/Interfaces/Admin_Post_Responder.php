<?php
/**
 * Admin_Post_Responder
 * 
 * Processes the response from an admin-post event
 */

namespace Mtgtools\Tasks\Admin_Post\Interfaces;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

interface Admin_Post_Responder
{
    /**
     * Handle success state of admin-post request
     */
    public function handle_success( array $result ) : void;

    /**
     * Handle error state of admin-post request
     */
    public function handle_error( \Exception $e ) : void;

    /**
     * Get wp prefix for hook action
     */
    public function get_wp_prefix() : string;

}   // End of interface