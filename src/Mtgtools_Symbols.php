<?php
/**
 * Mtgtools_Symbols
 * 
 * Mana symbols module
 */

namespace Mtgtools;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Symbols
{
    /**
     * Add WordPress hooks
     */
    public function add_hooks() : void
    {
        add_shortcode( 'mana_symbols', array( $this, 'parse_mana_symbols' ) );
    }

    /**
     * Parse mana symbols
     */
    private function parse_mana_symbols( array $atts, string $content = '' ) : string
    {
        return preg_replace( $this->get_patterns(), $this->get_replacements(), $content );
    }

    /**
     * Get mana symbol patterns
     */
    private function get_patterns() : array
    {
        
    }

}   // End of class