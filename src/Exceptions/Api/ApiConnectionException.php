<?php
/**
 * ApiConnectionException
 * 
 * Exception thrown when an HTTP connection cannot be established during an API call
 */

namespace Mtgtools\Exceptions\Api;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class ApiConnectionException extends ApiException
{

}