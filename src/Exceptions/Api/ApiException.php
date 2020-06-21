<?php
/**
 * ApiException
 * 
 * Exception thrown when an API request encounters an error
 */

namespace Mtgtools\Exceptions\Api;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class ApiException extends \RuntimeException
{

}