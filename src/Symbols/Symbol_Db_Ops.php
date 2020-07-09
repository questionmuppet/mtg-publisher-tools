<?php
/**
 * Symbol_Db_Ops
 * 
 * Handles database operations for mana symbols
 */

namespace Mtgtools\Symbols;
use Mtgtools\Abstracts\Data;
use Mtgtools\Updates\Db_Update_Checker;
use Mtgtools\Exceptions\Db as Exceptions;
use \wpdb;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Symbol_Db_Ops extends Data
{
    /**
     * Default properties
     */
    protected $defaults = array(
        'table' => 'mtgtools_symbols',
    );

    /**
     * Valid filter arguments for query
     */
    private $valid_filters = array(
        'plaintext',
        'english_phrase',
    );

    /**
     * Mana_Symbol cache
     */
    private $symbols = [];

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
    }

    /**
     * -----------------
     *   U P D A T E S
     * -----------------
     */

    /**
     * Get update checker for symbols table
     * 
     * @param Mana_Symbol[] $symbols    Symbols to check against extant database rows
     */
    public function get_update_checker( array $symbols ) : Db_Update_Checker
    {
        return new Db_Update_Checker(
            $this->db,
            $this->get_hash_map( $symbols ),
            [
                'db_table' => $this->get_table_short_name(),
                'key_column' => 'plaintext',
                'hash_column' => 'update_hash',
            ]
        );
    }

    /**
     * Get symbols hash map
     */
    private function get_hash_map( array $symbols ) : Symbols_Hash_Map
    {
        $map = new Symbols_Hash_Map();
        $map->add_records( $symbols );
        return $map;
    }

    /**
     * -------------
     *   Q U E R Y
     * -------------
     */
    
    /**
     * Get mana symbols with optional filters
     * 
     * @param array $filters    Associative array of "column" => "value" pairs to filter results
     * @return Mana_Symbol[]
     */
    public function get_mana_symbols( array $filters = [] ) : array
    {
        $symbols = [];
        foreach ( $this->get_symbol_rows( $filters ) as $data )
        {
            $key = $data['plaintext'];
            $symbols[] = $this->symbol_cached( $key )
                ? $this->get_cached_symbol( $key )
                : $this->create_symbol( $data );
        }
        return $symbols;
    }

    /**
     * Get rows from database matching filters
     */
    private function get_symbol_rows( array $filters = [] ) : array
    {
        $WHERE = count( $filters )
            ? $this->generate_where_clause( $filters )
            : '';
        
        return $this->db->get_results(
            "SELECT plaintext, english_phrase, svg_uri FROM {$this->get_table()} {$WHERE};",
            ARRAY_A
        );
    }

    /**
     * Generate WHERE clause from filters
     */
    private function generate_where_clause( array $filters ) : string
    {
        $conditions = [];
        foreach ( $filters as $key => $value )
        {
            if ( !$this->is_valid_filter( $key ) )
            {
                throw new Exceptions\DbException( get_called_class() . " tried to retrieve database rows using an unknown filter key '{$key}'." );
            }
            $conditions[] = $this->generate_where_condition( $key, $value );
        }
        return 'WHERE ' . implode( ' && ', $conditions );
    }
    
    /**
     * Generate WHERE condition
     */
    private function generate_where_condition( string $key, string $value ) : string
    {
        $column = sanitize_key( $key );
        return $this->db->prepare(
            "{$column} LIKE %s",
            '%' . $this->db->esc_like( $value ) . '%'
        );
    }
    
    /**
     * -------------
     *   C A C H E
     * -------------
     */
    
    /**
     * Check if symbol is cached
     */
    private function symbol_cached( string $key ) : bool
    {
        return array_key_exists( $key, $this->symbols );
    }

    /**
     * Get symbol from cache
     */
    private function get_cached_symbol( string $key ) : Mana_Symbol
    {
        return $this->symbols[ $key ];
    }

    /**
     * Create symbol and add to cache
     */
    private function create_symbol( array $args ) : Mana_Symbol
    {
        $symbol = new Mana_Symbol( $args );
        $this->symbols[ $symbol->get_plaintext() ] = $symbol;
        return $symbol;
    }

    /**
     * ---------------------------------
     *   S Y M B O L   C R E A T I O N
     * ---------------------------------
     */
    
    /**
     * Add a new mana symbol to the database
     * 
     * @return bool True if successful, false on error
     */
    public function add_symbol( Mana_Symbol $symbol ) : bool
    {
        if ( !$symbol->is_valid() )
        {
            throw new Exceptions\DbException( get_called_class() . " tried to add an invalid mana symbol with key '{$symbol->get_plaintext()}' to the database." );
        }
        $values = [
            'plaintext'      => $symbol->get_plaintext(),
            'english_phrase' => $symbol->get_english_phrase(),
            'svg_uri'        => $symbol->get_svg_uri(),
            'update_hash'    => $symbol->get_update_hash(),
        ];
        return boolval(
            $this->symbol_exists( $symbol->get_plaintext() )
            ? $this->update_symbol( $values )
            : $this->insert_symbol( $values )
        );
    }

    /**
     * Update an extant row in symbols table
     * 
     * @return int|false Rows updated, false on error
     */
    private function update_symbol( array $values )
    {
        return $this->db->update(
            $this->get_table(),
            $values,
            array( 'plaintext' => $values['plaintext'] ),   // Where clause
            '%s',                                           // Values format
            '%s'                                            // Where format
        );
    }
    
    /**
     * Insert new row into symbols table
     * 
     * @return int|false Rows inserted, false on error
     */
    private function insert_symbol( array $values )
    {
        return $this->db->insert( $this->get_table(), $values, '%s' );
    }

     /**
     * Delete a mana symbol from the database
     */
    public function delete_symbol( string $key ) : bool
    {
        $rows = $this->db->delete(
            $this->get_table(),
            [
                'plaintext' => $key
            ],
            '%s'
        );
        return boolval( $rows );
    }
    
    /**
     * Check if mana symbol is defined
     */
    private function symbol_exists( string $key ) : bool
    {
        $id = $this->db->get_var(
            $this->db->prepare(
                "SELECT id FROM {$this->get_table()} WHERE plaintext = %s",
                $key
            )
        );
        return !is_null( $id );
    }

    /**
     * -------------------------------
     *   T A B L E   C R E A T I O N
     * -------------------------------
     */

    /**
     * Create symbols table in db
     * 
     * @return bool True if successful, false on error
     */
    public function create_table() : bool
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS {$this->get_table()} (
                id smallint(5) UNSIGNED AUTO_INCREMENT,
                plaintext varchar(16) UNIQUE NOT NULL,
                english_phrase text NOT NULL,
                svg_uri text NOT NULL,
                update_hash text NOT NULL,
                PRIMARY KEY  (id)
            ) {$this->get_collate()};"
        );
    }

    /**
     * Remove symbols table from db
     * 
     * @return bool True if successful, false on error
     */
    public function drop_table() : bool
    {
        return $this->db->query( "DROP TABLE IF EXISTS {$this->get_table()};" );
    }

    /**
     * -----------------------
     *   P R O P E R T I E S
     * -----------------------
     */

    /**
     * Check if a query filter is valid
     */
    private function is_valid_filter( string $key ) : bool
    {
        return in_array( $key, $this->valid_filters );
    }

    /**
     * Get db table name
     */
    private function get_table() : string
    {
        return sanitize_key( $this->db->prefix . $this->get_table_short_name() );
    }

    /**
     * Get non-prefixed part of table name
     */
    private function get_table_short_name() : string
    {
        return $this->get_prop( 'table' );
    }

    /**
     * Get charset collation
     */
    private function get_collate() : string
    {
        return $this->db->get_charset_collate();
    }

}   // End of class