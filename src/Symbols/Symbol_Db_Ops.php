<?php
/**
 * Symbol_Db_Ops
 * 
 * Handles database CRUD for mana symbols
 */

namespace Mtgtools\Symbols;
use Mtgtools\Abstracts\Data;
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
     * Get a mana symbol by its plaintext string
     */
    public function get_symbol( string $key ) : Mana_Symbol
    {
        if ( !$this->symbol_exists( $key ) )
        {
            throw new \OutOfRangeException( get_called_class() . " tried to retrieve an undefined mana symbol. No record found in the database for symbol with key '{$key}'." );
        }
        return $this->get_mana_symbols()[ $key ];
    }


    /**
     * Get all mana symbols from database
     * 
     * @return Mana_Symbol[]
     */
    public function get_mana_symbols() : array
    {
        $symbols = [];
        $rows = $this->db->get_results( "SELECT * FROM {$this->get_table()}", ARRAY_A );
        foreach ( $rows as $data )
        {
            $new = new Mana_Symbol( $data );
            $symbols[ $new->get_pattern() ] = $new;
        }
        return $symbols;
    }
    
    /**
     * Add a new mana symbol to the database
     * 
     * @return bool True if successful, false on error
     */
    public function add_symbol( Mana_Symbol $symbol ) : bool
    {
        if ( !$symbol->is_valid() )
        {
            throw new Exceptions\DbException( get_called_class() . " tried to add an invalid mana symbol with key '{$symbol->get_pattern()}' to the database." );
        }
        if ( $this->symbol_exists( $symbol->get_pattern() ) )
        {
            throw new Exceptions\DbException( get_called_class() . " tried to add a duplicate mana symbol to the database. An entry already exists for key '{$symbol->get_pattern()}'." );
        }
        return (bool) $this->db->insert(
            $this->get_table(),
            [
                'plaintext'      => $symbol->get_pattern(),
                'english_phrase' => $symbol->get_english_phrase(),
                'svg_uri'        => $symbol->get_svg_uri(),
            ],
            '%s'
        );
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
     * Get db table name
     */
    private function get_table() : string
    {
        return sanitize_key( $this->db->prefix . $this->get_prop( 'table' ) );
    }

    /**
     * Get charset collation
     */
    private function get_collate() : string
    {
        return $this->db->get_charset_collate();
    }

}   // End of class