<?php
/**
 * Mtgtools_Symbols
 * 
 * Module that downloads and outputs graphical mana symbols
 */

namespace Mtgtools;

use Mtgtools\Abstracts\Module;
use Mtgtools\Db\Services\Symbol_Db_Ops;
use Mtgtools\Sources\Mtg_Data_Source;
use Mtgtools\Symbols\Mana_Symbol;
use Mtgtools\Exceptions\Db\NoResultsException;

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
        add_shortcode( 'oracle_text',                    array( $this, 'parse_oracle_text' ) );
        add_shortcode( 'mana_symbol',                     array( $this, 'insert_single_symbol' ) );
        add_action( 'mtgtools_dashboard_tabs',            array( $this, 'add_dash_tab' ), 50, 1 );
    }

    /**
     * Enqueue CSS/JS assets
     */
    public function enqueue_assets() : void
    {
        if ( $this->get_plugin_option( 'enqueue_component_styles' ) )
        {
            $style = $this->wp_tasks()->create_style([
                'key'  => 'mtgtools-symbols',
                'path' => 'mtgtools-symbols.css',
            ]);
            $style->enqueue();
        }
    }

    /**
     * Insert a single mana symbol from shortcode
     */
    public function insert_single_symbol( $atts, $content = '' ) : string
    {
        try
        {
            $key = sanitize_text_field( $atts['key'] ?? '' );
            $symbol = $this->db_ops()->get_symbol_by_plaintext( $key );
            return $symbol->get_markup( $this->wp_tasks() );
        }
        catch ( NoResultsException $e )
        {
            return $content;
        }
    }

    /**
     * Parse oracle text
     * 
     * @return string Content with plaintext mana symbols replaced by <img> markup
     */
    public function parse_oracle_text( $atts, $content = '' ) : string
    {
        $patterns = $replacements = [];
        foreach ( $this->db_ops()->get_mana_symbols() as $symbol )
        {
            if ( $symbol->is_valid() )
            {
                $patterns[]     = $symbol->get_pattern();
                $replacements[] = $symbol->get_markup( $this->wp_tasks() );
            }
        }
        return preg_replace( $patterns, $replacements, $this->wrap_reminder_text( $content ) );
    }

    /**
     * Wrap reminder text in <span>s so it can be styled
     */
    private function wrap_reminder_text( string $content ) : string
    {
        return preg_replace( 
            '/(\([^\)]+\))/',
            '<span class="mtg-reminder-text">$1</span>',
            $content
        );
    }

    /**
     * -----------------------------
     *   D A S H B O A R D   T A B
     * -----------------------------
     */

    /**
     * Add dashboard tab
     * 
     * @hooked mtgtools_dashboard_tabs
     */
    public function add_dash_tab( Mtgtools_Dashboard $dashboard ) : void
    {
        $dashboard->add_tab([
            'id'     => 'symbols',
            'title'  => 'Mana Symbols',
            'tables' => $this->get_dashboard_tables(),
        ]);
    }

    /**
     * Get dashboard tables
     * 
     * @return Table_Data[]
     */
    private function get_dashboard_tables() : array
    {
        return array(
            'symbol_list' => $this->wp_tasks()->create_table([
                'id'           => 'symbol_list',
                'row_callback' => array( $this, 'get_symbol_list_data' ),
                'fields'       => [
                    'plaintext' => [
                        'title' => 'Text',
                        'width' => 70,
                    ],
                    'symbol'    => [
                        'width' => 70,
                    ],
                    'english'   => [
                        'title' => 'English Phrase',
                        'width' => 300,
                    ],
                ],
            ]),
        );
    }

    /**
     * Get symbol list data for tables
     */
    public function get_symbol_list_data( string $filter = '' ) : array
    {
        $filters = array_filter([ 'plaintext' => $filter ]);
        $rows = [];
        foreach ( $this->db_ops()->get_mana_symbols( $filters ) as $symbol )
        {
            $rows[] = array(
                'plaintext' => $symbol->get_plaintext(),
                'symbol'    => $symbol->get_markup( $this->wp_tasks() ),
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
            $this->db_ops()->add_symbol( $symbol );
        }
    }

    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * Get symbol db ops
     */
    private function db_ops() : Symbol_Db_Ops
    {
        return $this->db_ops;
    }

}   // End of class