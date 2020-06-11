<?php
/**
 * Http500StatusException
 * 
 * Exception thrown when an HTTP call returns a 5xx status (server error)
 */

namespace Mtgtools\Exceptions\Http;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Http500StatusException extends HttpStatusException
{

}