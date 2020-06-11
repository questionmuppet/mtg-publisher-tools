<?php
/**
 * HttpRequestException
 * 
 * Exception thrown when an external HTTP request fails
 */

namespace Mtgtools\Exceptions\Http;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class HttpRequestException extends \RuntimeException
{

}