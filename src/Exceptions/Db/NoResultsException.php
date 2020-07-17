<?php
/**
 * NoResultsException
 * 
 * Thrown when no results are returned for a query expecting at least one
 */

namespace Mtgtools\Exceptions\Db;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class NoResultsException extends DbException
{

}