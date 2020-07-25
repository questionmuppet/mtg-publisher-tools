<?php
/**
 * CacheException
 * 
 * Exception thrown when a request for cached data fails.
 */

namespace Mtgtools\Exceptions\Cache;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class CacheException extends \RuntimeException
{

}