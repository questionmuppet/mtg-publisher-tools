<?php
/**
 * HttpStatusException
 * 
 * Exception thrown when an HTTP call returns an invalid status
 */

namespace Mtgtools\Exceptions\Http;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class HttpStatusException extends HttpRequestException
{

}