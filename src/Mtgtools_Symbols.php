<?php
/**
 * Mtgtools_Symbols
 * 
 * Module that downloads and outputs graphical mana symbols
 */

namespace Mtgtools;
use Mtgtools\Symbols\Symbol_Db_Ops;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Symbols
{
    /**
     * Class for handling database CRUD
     */
    private $db_ops;

    /**
     * Constructor
     */
    public function __construct( Symbol_Db_Ops $db_ops )
    {
        $this->db_ops = $db_ops;
    }

    /**
     * Add WordPress hooks
     */
    public function add_hooks() : void
    {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_shortcode( 'mana_symbols',    array( $this, 'parse_mana_symbols' ) );
    }

    /**
     * Enqueue CSS/JS assets
     */
    public function enqueue_assets() : void
    {
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
                $replacements[] = $symbol->get_markup();
            }
        }
        return preg_replace( $patterns, $replacements, $content );
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