<?php
/**
 * Admin_Post_Responder
 * 
 * Interface that sends admin-post responses to user
 */

namespace Mtgtools\Admin_Post\Interfaces;
use \Exception;

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
    public function handle_error( Exception $e ) : void;

    /**
     * Get wp prefix for hook action
     */
    public function get_wp_prefix() : string;

}   // End of interface