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
     * Add WordPress hooks
     */
    public function add_hooks() : void
    {
        add_action( 'wp_enqueue_scripts',                 array( $this, 'enqueue_assets' ) );
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
                $patterns[] = $symbol->get_pattern();
                $replacements[] = $this->get_markup( $symbol );
            }
        }
        return preg_replace( $patterns, $replacements, $content );
    }

    /**
     * Get mana symbol HTML
     */
    private function get_markup( Mana_Symbol $symbol ) : string
    {
        ob_start();
        $this->mtgtools()->load_template([
            'path' => 'components/mana-symbol.php',
            'vars' => array( 'symbol' => $symbol )
        ]);
        return ob_get_clean();
    }

    /**
     * Add dashboard tab
     */
    public function add_dash_tab( array $defs ) : array
    {
        $defs = array_merge([
            'symbols' => [
                'title' => 'Mana Symbols',
            ],
        ], $defs );
        return $defs;
    }

    /**
     * ---------------------------
     *   S Y M B O L   C A C H E
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
     * ---------------------
     *   D B   T A B L E S
     * ---------------------
     */

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