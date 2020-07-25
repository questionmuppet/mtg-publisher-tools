<?php
/**
 * Db_Ops
 * 
 * Abstract class for handling database operations
 */

namespace Mtgtools\Db;

use \wpdb;
use Mtgtools\Db\Sql_Tokens\Sql_Token;
use Mtgtools\Exceptions\Db as Exceptions;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Db_Ops
{
    /**
     * Rows affected from last query
     */
    private $rows_affected = 0;

    /**
     * Database class
     */
    private $db;

    /**
     * Constructor
     */
    public function __construct( wpdb $db )
    {
        $this->db = $db;
    }
    
    /**
     * -----------------------------
     *   G E N E R I C   Q U E R Y
     * -----------------------------
     */

    /**
     * Execute a generic, unescaped SQL query
     * 
     * @param string $query     Raw query string. Calling methods are responsible for sanitization.
     * @return int              Rows affected
     * @throws SqlErrorException
     */
    protected function execute_query( string $query ) : int
    {
        $result = $this->db()->query( $query );
        if ( false === $result )
        {
            throw new Exceptions\SqlErrorException(
                sprintf(
                    "%s encountered a SQL error trying to execute a query. %s",
                    get_called_class(),
                    $this->db()->last_error
                )
            );
        }
        // wpdb::query() returns int|true on success
        return $this->rows_affected = (int) $result;
    }

    /**
     * Get rows affected from latest query
     */
    public function get_rows_affected() : int
    {
        return $this->rows_affected;
    }
    
    /**
     * ---------------------------
     *   T R A N S A C T I O N S
     * ---------------------------
     */

    /**
     * Start transaction
     */
    protected function start_transaction() : void
    {
        $this->db()->query( 'SET autocommit = 0;' );
        $this->db()->query( 'START TRANSACTION;' );
    }

    /**
     * Commit transaction
     */
    protected function commit_transaction() : void
    {
        $this->db()->query( 'COMMIT;' );
        $this->db()->query( 'SET autocommit = 1;' );
    }

    /**
     * Rollback transaction
     */
    protected function rollback_transaction() : void
    {
        $this->db()->query( 'ROLLBACK;' );
        $this->db()->query( 'SET autocommit = 1;' );
    }
    
    /**
     * ---------------------------
     *   S A N I T I Z A T I O N
     * ---------------------------
     */
    
    /**
     * Get a whitelisted token safe for use in SQL queries
     */
    protected function get_whitelisted_token( string $context, Sql_Token $token ) : string
    {
        if ( !$token->is_safe_for( $context ) )
        {
            throw new \DomainException(
                sprintf(
                    "%s was provided an invalid SQL token for use in a query. %s token is not whitelisted for '%s' context.",
                    get_called_class(),
                    $token->get_name(),
                    $context
                )
            );
        }
        return $token->get_token();
    }

    /**
     * Strip backticks from a SQL keyname
     */
    protected function strip_backticks( string $keyname ) : string
    {
        return preg_replace( '/`/', '', $keyname );
    }
    
    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * Get charset collation
     */
    protected function get_collate() : string
    {
        return $this->db()->get_charset_collate();
    }

    /**
     * Get WordPress database class
     */
    protected function db() : wpdb
    {
        return $this->db;
    }

}   // End of class