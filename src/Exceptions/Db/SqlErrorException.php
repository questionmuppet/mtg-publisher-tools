<?php
/**
 * SqlErrorException
 * 
 * Thrown when a SQL error occurs during execution of a query
 */

namespace Mtgtools\Exceptions\Db;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class SqlErrorException extends DbException
{

}