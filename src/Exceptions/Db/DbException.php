<?php
/**
 * DbException
 * 
 * Exception thrown when a database operation yields an error
 */

namespace Mtgtools\Exceptions\Db;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class DbException extends \RuntimeException
{

}