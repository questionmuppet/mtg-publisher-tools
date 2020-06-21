<?php
/**
 * ApiStatusException
 * 
 * Exception thrown when an API returns an unsuccessful HTTP status code
 */

namespace Mtgtools\Exceptions\Api;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class ApiStatusException extends ApiException
{

}