<?php
/**
 * HttpConnectionException
 * 
 * Exception thrown when a connection cannot be made for an HTTP request
 */

namespace Mtgtools\Exceptions\Http;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class HttpConnectionException extends \RuntimeException
{

}