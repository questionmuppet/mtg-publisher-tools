<?php
/**
 * Remote_Request
 * 
 * Interface for making requests to external resources
 */

namespace Mtgtools\Interfaces;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

interface Remote_Request
{
    /**
     * Get the data returned by the request
     */
    public function get_response_body() : array;

    /**
     * Get status code of the request
     */
    public function get_status_code() : string;

    /**
     * Get status message of the request
     */
    public function get_status_message() : string;

}   // End of interface