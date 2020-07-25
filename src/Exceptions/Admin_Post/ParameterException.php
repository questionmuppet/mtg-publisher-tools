<?php
/**
 * ParameterException
 * 
 * Exception thrown when admin-post parameters are missing or malformed
 */

namespace Mtgtools\Exceptions\Admin_Post;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class ParameterException extends PostHandlerException
{
    /**
     * Default response status
     */
    protected $http_code = 400;
    protected $http_title = 'Bad Request';

}   // End of class