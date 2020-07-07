<?php
/**
 * Db_Update_Checker
 * 
 * Compares hashes in a database table against a data source to determine records in need of update
 */

namespace Mtgtools\Updates;

use Mtgtools\Abstracts\Data;
use Mtgtools\Interfaces\Hash_Map;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Db_Update_Checker extends Data
{
    /**
     * Required properties
     */
    protected $required = [
        'db_table',
        'key_column',
        'hash_column',
    ];

    /**
     * Defaults
     */
    protected $defaults = [
        'chunk_size' => 100,
    ];

    /**
     * Records to update
     */
    private $records;

    /**
     * Dependencies
     */
    private $db;
    private $hash_map;

    /**
     * Constructor
     */
    public function __construct( \wpdb $db, Hash_Map $hash_map, array $props )
    {
        $this->db = $db;
        $this->hash_map = $hash_map;
        parent::__construct( $props );
    }

    /**
     * -----------------
     *   R E C O R D S
     * -----------------
     */

    /**
     * Check for updates
     */
    public function has_updates() : bool
    {
        $total = 0;
        foreach ( $this->get_records() as $item_list )
        {
            $total += count( $item_list );
        }
        return boolval( $total );
    }

    /**
     * Get records to add to db
     */
    public function records_to_add() : array
    {
        return $this->get_records()['add'];
    }

    /**
     * Get records to update in db
     */
    public function records_to_update() : array
    {
        return $this->get_records()['update'];
    }

    /**
     * Get records to remove from db
     */
    public function records_to_delete() : array
    {
        return $this->get_records()['delete'];
    }

    /**
     * Get records from comparison
     */
    private function get_records() : array
    {
        if ( !isset( $this->records ) )
        {
            $this->records = $this->execute_comparison();
        }
        return $this->records;
    }

    /**
     * -----------------------
     *   C O M P A R I S O N
     * -----------------------
     */

    /**
     * Execute the comparison and return the resulting records
     */
    private function execute_comparison() : array
    {
        $this->start_transaction();
        $records = [
            'add' => $this->find_add_rows(),
            'update' => $this->find_update_rows(),
            'delete' => $this->find_delete_rows(),
        ];
        $this->end_transaction();
        return $records;
    }

    /**
     * Find rows to add to db
     */
    private function find_add_rows() : array
    {
        // Get sanitized keys
        $db_table = $this->get_table_name();
        $hash_table = $this->get_hash_table_name();
        $key = $this->get_identifier_column();
        $db_hash = $this->get_hash_column();

        return $this->db->get_col(
            "SELECT hash_key FROM $hash_table
            LEFT JOIN $db_table ON hash_key = $key
            WHERE $db_hash IS NULL;"
        );
    }

    /**
     * Find rows to update in db
     */
    private function find_update_rows() : array
    {
        // Get sanitized keys
        $db_table = $this->get_table_name();
        $hash_table = $this->get_hash_table_name();
        $key = $this->get_identifier_column();
        $db_hash = $this->get_hash_column();

        return $this->db->get_col(
            "SELECT $key FROM $hash_table
            LEFT JOIN $db_table ON $key = hash_key
            WHERE $db_hash != hash_value;"
        );
    }

    /**
     * Find rows to delete from db
     */
    private function find_delete_rows() : array
    {
        // Get sanitized keys
        $db_table = $this->get_table_name();
        $hash_table = $this->get_hash_table_name();
        $key = $this->get_identifier_column();
        $db_hash = $this->get_hash_column();

        return $this->db->get_col(
            "SELECT $key FROM $db_table
            LEFT JOIN $hash_table ON $key = hash_key
            WHERE hash_value IS NULL;"
        );
    }

    /**
     * -------------------------
     *   T R A N S A C T I O N
     * -------------------------
     */

    /**
     * Start SQL transaction
     */
    private function start_transaction() : void
    {
        $this->db->query( 'SET autocommit = 0;' );
		$this->db->query( 'START TRANSACTION;' );
        $this->create_hash_table();
        $this->populate_hash_table();
    }

    /**
     * End SQL transaction
     */
    private function end_transaction() : void
    {
        $this->db->query( 'ROLLBACK;' );
    }

    /**
     * -----------------------
     *   H A S H   T A B L E
     * -----------------------
     */

    /**
     * Create temporary hash table
     */
    private function create_hash_table() : void
    {
        $this->db->query(
            "CREATE TEMPORARY TABLE {$this->get_hash_table_name()} (
                id int(10) UNSIGNED AUTO_INCREMENT,
                hash_key varchar(256) UNIQUE NOT NULL,
                hash_value text NOT NULL,
                PRIMARY KEY (id)
            );"
        );
    }

    /**
     * Populate hash table using data source
     */
    private function populate_hash_table() : void
    {
        foreach ( $this->get_hash_in_chunks() as $chunk )
        {
            $this->insert_hash_pairs( $chunk );
        }
    }
    
    /**
     * Get hash in chunks
     */
    private function get_hash_in_chunks() : array
    {
        return array_chunk(
            $this->hash_map->get_map(),
            $this->get_chunk_size(),
            true    // Preserve keys
        );
    }

    /**
     * Insert a group of key-hash pairs
     */
    private function insert_hash_pairs( array $rows ) : void
    {
        $values = [];
        foreach ( $rows as $key => $hash )
        {
            $values[] = $this->db->prepare( "(%s, %s)", $key, $hash );
        }
        $this->db->query(
            sprintf(
                "INSERT INTO {$this->get_hash_table_name()}
                (hash_key, hash_value)
                VALUES %s;",
                implode( ',', $values )
            )
        );
    }

    /**
     * -----------------------
     *   T A B L E   K E Y S
     * -----------------------
     */

    /**
     * Get sanitized name of temporary hash table
     */
    private function get_hash_table_name() : string
    {
        return sanitize_key( $this->get_table_name() . '_comparison_hash_map' );
    }

    /**
     * Get sanitized db table name
     */
    private function get_table_name() : string
    {
        return sanitize_key( $this->db->prefix . $this->get_prop( 'db_table' ) );
    }

    /**
     * Get sanitized column name for identifier key
     */
    private function get_identifier_column() : string
    {
        return sanitize_key( $this->get_prop( 'key_column' ) );
    }

    /**
     * Get sanitized column name for values hash
     */
    private function get_hash_column() : string
    {
        return sanitize_key( $this->get_prop( 'hash_column' ) );
    }

    /**
     * -----------------
     *   O P T I O N S
     * -----------------
     */

    /**
     * Get chunk size for inserting hashes
     */
    private function get_chunk_size() : int
    {
        return $this->get_prop( 'chunk_size' );
    }

}   // End of class