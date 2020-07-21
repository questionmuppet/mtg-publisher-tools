<?php
/**
 * Sql_Current_Timestamp
 * 
 * Allows safe insertion of the current timestamp into SQL queries
 */

namespace Mtgtools\Db\Sql_Tokens;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Sql_Current_Timestamp implements Sql_Token
{
    /**
     * Whitelisted contexts
     */
    private $contexts = [
        'column_value'
    ];

    /**
     * Check if token is safe for the given context
     */
    public function is_safe_for( string $context ) : bool
    {
        return in_array( $context, $this->contexts );
    }

    /**
     * Get token string
     */
    public function get_token() : string
    {
        return 'NOW()';
    }

    /**
     * Get human-readable name
     */
    public function get_name() : string
    {
        return 'Current Timestamp';
    }

}   // End of class