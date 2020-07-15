<?php
/**
 * ExpiredDataException
 * 
 * Exception thrown when data requested from db cache has expired
 */

namespace Mtgtools\Exceptions\Cache;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class ExpiredDataException extends CacheException
{

}