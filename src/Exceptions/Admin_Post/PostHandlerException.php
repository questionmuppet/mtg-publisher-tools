<?php
/**
 * PostHandlerException
 * 
 * Exception thrown when a user request to the WordPress admin fails
 */

namespace Mtgtools\Exceptions\Admin_Post;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class PostHandlerException extends \RuntimeException
{
    /**
     * HTTP response status
     */
    protected $http_code = 500;
    protected $http_title = 'Internal Server Error';

    /**
     * Add HTTP response status info
     */
    public function add_http_status( int $code, string $title ) : void
    {
        $this->http_code = $code;
        $this->http_title = $title;
    }

    /**
     * Get full HTTP status string
     */
    public function get_http_status() : string
    {
        return sprintf(
            "%s %s",
            $this->get_http_response_code(),
            $this->get_http_response_title()
        );
    }

    /**
     * Get HTTP response code
     */
    public function get_http_response_code() : int
    {
        return $this->http_code;
    }

    /**
     * Get HTTP response title
     */
    public function get_http_response_title() : string
    {
        return $this->http_title;
    }

}   // End of class