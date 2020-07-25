<?php
/**
 * AuthorizationException
 * 
 * Exception thrown when an admin-post permission or security check fails
 */

namespace Mtgtools\Exceptions\Admin_Post;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class AuthorizationException extends PostHandlerException
{
    /**
     * Default response status
     */
    protected $http_code = 403;
    protected $http_title = 'Forbidden';

}   // End of class