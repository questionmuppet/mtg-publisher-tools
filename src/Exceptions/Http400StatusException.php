<?php
/**
 * Http400StatusException
 * 
 * Exception thrown when an HTTP call returns a 4xx status (client error)
 */

namespace Mtgtools\Exceptions;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Http400StatusException extends HttpStatusException
{

}