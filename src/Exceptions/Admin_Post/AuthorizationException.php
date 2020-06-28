<?php
/**
 * AuthorizationException
 * 
 * Exception thrown when a permission or security check fails
 */

namespace Mtgtools\Exceptions\Admin_Post;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class AuthorizationException extends PostHandlerException
{

}