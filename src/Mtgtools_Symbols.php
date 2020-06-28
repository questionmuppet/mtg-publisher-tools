<?php
/**
 * Mtgtools_Symbols
 * 
 * Module that downloads and outputs graphical mana symbols
 */

namespace Mtgtools;
use Mtgtools\Abstracts\Module;
use Mtgtools\Symbols\Symbol_Db_Ops;
use Mtgtools\Interfaces\Mtg_Data_Source;
use Mtgtools\Symbols\Mana_Symbol;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Symbols extends Module
{
    /**
     * Class for handling database CRUD
     */
    private $db_ops;

    /**
     * MTG data source
     */
    private $source;

    /**
     * Constructor
     */
    public function __construct( Symbol_Db_Ops $db_ops, Mtg_Data_Source $source, $plugin )
    {
        $this->db_ops = $db_ops;
        $this->source = $source;
        parent::__construct( $plugin );
    }

    /**
     * -------------------
     *   W P   H O O K S
     * -------------------
     */

    /**
     * Add WordPress hooks
     */
    public function add_hooks() : void
    {
        add_action( 'wp_enqueue_scripts',                 array( $this, 'enqueue_assets' ) );
        add_action( 'admin_enqueue_scripts',              array( $this, 'enqueue_assets' ) );
        add_shortcode( 'mana_symbols',                    array( $this, 'parse_mana_symbols' ) );
        add_filter( 'mtgtools_dashboard_tab_definitions', array( $this, 'add_dash_tab' ), 10 );
    }

    /**
     * Enqueue CSS/JS assets
     */
    public function enqueue_assets() : void
    {
        $this->mtgtools()->add_style([
            'key'  => 'mtgtools-symbols',
            'path' => 'mtgtools-symbols.css',
        ]);
    }

    /**
     * Parse mana symbols
     * 
     * @return string Content with plaintext mana symbols replaced by <img> markup
     */
    public function parse_mana_symbols( $atts, $content = '' ) : string
    {
        $patterns = $replacements = [];
        foreach ( $this->db_ops->get_mana_symbols() as $symbol )
        {
            if ( $symbol->is_valid() )
            {
                $patterns[]     = $symbol->get_pattern();
                $replacements[] = $symbol->get_markup();
            }
        }
        return preg_replace( $patterns, $replacements, $content );
    }

    /**
     * -----------------------------
     *   D A S H B O A R D   T A B
     * -----------------------------
     */

    /**
     * Add dashboard tab
     */
    public function add_dash_tab( array $defs ) : array
    {
        $defs = array_merge([
            'symbols' => [
                'title'              => 'Mana Symbols',
                'scripts'            => [
                    [
                        'key'  => 'mtgtools-data-table',
                        'path' => 'data-tables.js',
                        'data' => [
                            'mtgtoolsDataTable' => [ 'nonce' => wp_create_nonce('mtgtools_update_table') ]
                        ]
                    ]
                ],
                'table_row_callback' => array( $this, 'get_table_rows' ),
                'table_fields'       => [
                    'plaintext' => [
                        'title' => 'Text',
                        'width' => 70,
                    ],
                    'symbol'    => [
                        'width' => 70,
                    ],
                    'english'   => [
                        'title' => 'English Phrase',
                        'width' => 245,
                    ],
                ],
            ],
        ], $defs );
        return $defs;
    }

    /**
     * Get symbol row data
     */
    public function get_table_rows( string $filter = '' ) : array
    {
        $filters = array_filter([ 'plaintext' => $filter ]);
        $rows = [];
        foreach ( $this->db_ops->get_mana_symbols( $filters ) as $symbol )
        {
            $rows[] = array(
                'plaintext' => $symbol->get_plaintext(),
                'symbol'    => $symbol->get_markup(),
                'english'   => $symbol->get_english_phrase(),
            );
        }
        return $rows;
    }

    /**
     * ---------------------------
     *   I N S T A L L A T I O N
     * ---------------------------
     */

    /**
     * Import all symbols from external source
     */
    public function import_symbols() : void
    {
        foreach ( $this->source->get_mana_symbols() as $symbol )
        {
            $this->db_ops->add_symbol( $symbol );
        }
    }

    /**
     * Install database tables
     * 
     * @hooked Plugin activation
     */
    public function install_db_tables() : void
    {
        $this->db_ops->create_table();
    }

    /**
     * Delete database tables
     * 
     * @hooked Plugin uninstall
     */
    public function delete_db_tables() : void
    {
        $this->db_ops->drop_table();
    }

}   // End of class