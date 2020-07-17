<?php
/**
 * MissingDataException
 * 
 * Exception thrown when data requested from cache is missing
 */

namespace Mtgtools\Exceptions\Cache;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class MissingDataException extends CacheException
{

}