<?php
/**
 * ExternalCallException
 * 
 * Exception thrown when an admin-post action fails due to a failed external call
 */

namespace Mtgtools\Exceptions\Admin_Post;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class ExternalCallException extends PostHandlerException
{

}   // End of class