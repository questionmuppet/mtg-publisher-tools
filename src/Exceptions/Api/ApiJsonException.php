<?php
/**
 * ApiJsonException
 * 
 * Exception thrown when an API returns malformed JSON
 */

namespace Mtgtools\Exceptions\Api;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class ApiJsonException extends ApiException
{

}