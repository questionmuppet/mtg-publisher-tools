<?php
/**
 * Mana_Symbol
 * 
 * A single mana symbol with plaintext representation and svg
 */

namespace Mtgtools\Symbols;
use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mana_Symbol extends Data
{
    /**
     * Required properties
     */
    protected $required = array(
        'plaintext',
        'english_phrase',
        'svg_uri',
    );

    /**
     * Check if symbol is valid for text replacement
     */
    public function is_valid() : bool
    {
        return strlen( $this->get_pattern() );
    }

    /**
     * Get plaintext pattern to find in text
     */
    public function get_pattern() : string
    {
        return $this->get_string_prop( 'plaintext' );
    }

    /**
     * Get sanitized HTML markup for svg image
     */
    public function get_markup() : string
    {
        return sprintf(
            '<img class="%s" alt="%s" src="%s" />',
            esc_attr( $this->get_css_class() ),
            esc_attr( $this->get_english_phrase() ),
            esc_url( $this->get_svg_uri() )
        );
    }

    /**
     * Get CSS class string for <img> tag
     */
    private function get_css_class() : string
    {
        return 'mtg-symbol';
    }

    /**
     * Get English phrase (for alt text, et.al.)
     */
    public function get_english_phrase() : string
    {
        return $this->get_string_prop( 'english_phrase' );
    }

    /**
     * Get uri to svg image
     */
    public function get_svg_uri() : string
    {
        return $this->get_string_prop( 'svg_uri' );
    }

}   // End of class