<?php
/**
 * Sql_Token
 * 
 * A short snippet of code marked safe for use in SQL queries
 */

namespace Mtgtools\Db\Sql_Tokens;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

interface Sql_Token
{
    /**
     * Check if token is safe for the given context
     */
    public function is_safe_for( string $context ) : bool;

    /**
     * Get token string to use in SQL statement
     */
    public function get_token() : string;

    /**
     * Get human-readable name for output
     */
    public function get_name() : string;

}   // End of interface