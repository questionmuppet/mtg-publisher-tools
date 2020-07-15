<?php
/**
 * Db_Ops
 * 
 * Abstract class for handling database operations on a custom table
 */

namespace Mtgtools\Abstracts;

use Mtgtools\Exceptions\Db as Exceptions;
use \wpdb;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Db_Ops extends Data
{
    /**
     * Db tables
     */
    protected $tables = [];

    /**
     * Valid filter arguments for query
     */
    protected $valid_filters = [];

    /**
     * Database class
     */
    private $db;

    /**
     * Constructor
     */
    public function __construct( wpdb $db, array $props = [] )
    {
        $this->db = $db;
        parent::__construct( $props );
        $this->tables = array_replace( $this->tables, $this->get_prop( 'tables' ) ?? [] );
    }

    /**
     * -----------------
     *   R E C O R D S
     * -----------------
     */

    /**
     * Update a table row, or create it if it doesn't exist
     * 
     * @param string $params['table']
     * @param array $params['identifiers']
     * @param array $params['values']
     * @return bool True if a record was updated or inserted, false if not
     */
    protected function save_record( array $params ) : bool
    {
        $params = array_replace(
            [
                'table' => '',
                'identifiers' => [],
                'values' => [],
            ],
            $params
        );
        $params['table'] = $this->get_table_name( $params['table'] );
        
        $filters = [];
        foreach ( $params['identifiers'] as $key )
        {
            $filters[ $key ] = $params['values'][ $key ];
        }
        $params['identifiers'] = $filters;

        return $this->record_exists( $params['table'], $params['identifiers'] )
            ? $this->update_row( $params )
            : $this->insert_row( $params );
    }

    /**
     * Check if a record exists in the db
     */
    private function record_exists( string $table, array $identifiers ) : bool
    {
        return (bool) $this->db()->get_var(
            sprintf(
                "SELECT id FROM %s WHERE %s",
                sanitize_key( $table ),
                $this->where_conditions( $identifiers )
            )
        );
    }

    /**
     * Update row in table
     * 
     * @return bool Success status
     */
    private function update_row( array $params ) : bool
    {
        return (bool) $this->db()->update(
            $params['table'],
            $params['values'],
            $params['identifiers'],     // WHERE clause
            '%s',                       // Format
            '%s'                        // WHERE format
        );
    }

    /**
     * Insert row into table
     * 
     * @return bool Success status
     */
    private function insert_row( array $params ) : bool
    {
        return (bool) $this->db()->insert(
            $params['table'],
            $params['values'],
            '%s'    // Format
        );
    }

    /**
     * -------------------------------
     *   S E A R C H   F I L T E R S
     * -------------------------------
     */

    /**
     * Generate WHERE conditions from filters
     * 
     * @param array $filters    One or more "column" => "value" pairs to use as conditions
     * @param bool $exact       Whether to search for an exact match
     * @return string           Sanitized expression for use in WHERE statement
     */
    protected function where_conditions( array $filters, bool $exact = true ) : string
    {
        $conditions = [];
        foreach ( $filters as $key => $value )
        {
            if ( !$this->is_valid_filter( $key ) )
            {
                throw new Exceptions\DbException( get_called_class() . " tried to retrieve database rows using an unknown filter key '{$key}'." );
            }
            $conditions[] = $this->generate_where_condition([
                'key' => $key,
                'value' => $value,
                'exact' => $exact,
            ]);
        }
        return implode( ' && ', $conditions );
    }
    
    /**
     * Generate WHERE condition
     */
    private function generate_where_condition( array $params ) : string
    {
        $params = array_replace([
            'key' => '',
            'value' => '',
            'exact' => true,
        ], $params );

        return $params['exact']
            ? $this->prepare_exact_match( $params['key'], $params['value'] )
            : $this->prepare_fuzzy_match( $params['key'], $params['value'] );
    }

    /**
     * Prepare an exact-match conditional
     */
    private function prepare_exact_match( string $key, string $value ) : string
    {
        $column = sanitize_key( $key );
        return $this->db()->prepare( "{$column} = %s", $value );
    }

    /**
     * Prepare a fuzzy-match conditional
     */
    private function prepare_fuzzy_match( string $key, string $value ) : string
    {
        $column = sanitize_key( $key );
        return $this->db()->prepare(
            "{$column} LIKE %s",
            '%' . $this->db()->esc_like( $value ) . '%'
        );
    }


    /**
     * Check if a query filter is valid
     */
    private function is_valid_filter( string $key ) : bool
    {
        return in_array( $key, $this->valid_filters );
    }

    /**
     * ---------------
     *   T A B L E S
     * ---------------
     */

    /**
     * Get table name by key
     */
    protected function get_table_name( string $key ) : string
    {
        if ( !$this->table_defined( $key ) )
        {
            throw new \OutOfRangeException(
                sprintf(
                    "Attempted a database operation on an undefined table. No table defined in class '%s' with key '%s'.",
                    get_called_class(),
                    $key
                )
            );
        }
        return $this->db()->prefix . $this->tables[ $key ];
    }

    /**
     * Check if table is defined
     */
    private function table_defined( string $key ) : bool
    {
        return array_key_exists( $key, $this->tables );
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