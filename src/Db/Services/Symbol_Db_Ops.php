<?php
/**
 * Symbol_Db_Ops
 * 
 * Handles database operations for mana symbols
 */

namespace Mtgtools\Db\Services;

use Mtgtools\Db;
use Mtgtools\Exceptions\Db as Exceptions;

use Mtgtools\Symbols\Mana_Symbol;
use Mtgtools\Symbols\Symbols_Hash_Map;
use Mtgtools\Updates\Db_Update_Checker;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Symbol_Db_Ops extends Db\Db_Ops
{
    /**
     * Db tables
     */
    private $table;

    /**
     * Unprefixed table name
     */
    private $table_keyname = 'mtgtools_symbols';

    /**
     * Mana_Symbol cache
     */
    private $symbols = [];

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
            $this->db(),
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
     * Get a single mana symbol by its plaintext key
     * 
     * @throws NoResultsException
     */
    public function get_symbol_by_plaintext( string $key ) : Mana_Symbol
    {
        if ( $this->symbol_cached( $key ) )
        {
            return $this->get_cached_symbol( $key );
        }
        return $this->create_symbol(
            $this->db_table()->get_record([
                'plaintext' => $key
            ])
        );
    }
    
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
        return $this->db_table()->find_records([
            'filters' => $filters,
            'exact' => false,
        ]);
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
     * Add a new mana symbol or update an old one
     * 
     * @return int Number of rows affected
     */
    public function add_symbol( Mana_Symbol $symbol ) : int
    {
        if ( !$symbol->is_valid() )
        {
            throw new \DomainException(
                sprintf(
                    "%s tried to add an invalid mana symbol with key '%s' to the database.",
                    get_called_class(),
                    $symbol->get_plaintext()
                )
            );
        }
        $this->db_table()->save_record([
            'plaintext'      => $symbol->get_plaintext(),
            'english_phrase' => $symbol->get_english_phrase(),
            'svg_uri'        => $symbol->get_svg_uri(),
            'update_hash'    => $symbol->get_update_hash(),
        ]);
        return $this->db_table()->get_rows_affected();
    }

    /**
     * Delete a mana symbol from the database
     */
    public function delete_symbol( string $key ) : bool
    {
        $rows = $this->db()->delete(
            $this->get_table(),
            [
                'plaintext' => $key
            ],
            '%s'
        );
        return boolval( $rows );
    }

    /**
     * ---------------------------
     *   I N S T A L L A T I O N
     * ---------------------------
     */

    /**
     * Create symbols table in db
     * 
     * @throws SqlErrorException
     */
    public function create_table() : void
    {
        $this->execute_query(
            "CREATE TABLE IF NOT EXISTS {$this->get_table()} (
                id SMALLINT UNSIGNED AUTO_INCREMENT,
                plaintext VARCHAR(16) UNIQUE NOT NULL,
                english_phrase TEXT NOT NULL,
                svg_uri TEXT NOT NULL,
                update_hash TEXT NOT NULL,
                PRIMARY KEY  (id)
            ) {$this->get_collate()};"
        );
    }

    /**
     * Remove symbols table from db
     * 
     * @throws SqlErrorException
     */
    public function drop_table() : void
    {
        $this->execute_query( "DROP TABLE IF EXISTS {$this->get_table()};" );
    }

    /**
     * ---------------
     *   T A B L E S
     * ---------------
     */

    /**
     * Get db table name
     */
    private function get_table() : string
    {
        return $this->db_table()->get_table_name();
    }

    /**
     * Get symbols db table
     */
    private function db_table() : Db\Db_Table
    {
        if ( !isset( $this->table ) )
        {
            $this->table = new Db\Db_Table( $this->db(), [
                'table' => $this->get_table_short_name(),
                'filters' => [
                    'plaintext',
                    'english_phrase',
                ],
            ]);
        }
        return $this->table;
    }
    
    /**
     * Get non-prefixed part of table name
     */
    private function get_table_short_name() : string
    {
        return $this->table_keyname;
    }

}   // End of class