<?php
/**
 * Mana_Symbol
 * 
 * A single mana symbol with plaintext representation and svg
 */

namespace Mtgtools\Symbols;

use Mtgtools\Abstracts\Data;
use Mtgtools\Wp_Task_Library;

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
        return strlen( $this->get_plaintext() );
    }

    /**
     * Get regexp pattern to find in text
     */
    public function get_pattern() : string
    {
        return sprintf(
            "/%s/",
            preg_quote( $this->get_plaintext(), '/' )
        );
    }

    /**
     * Get HTML markup
     */
    public function get_markup( Wp_Task_Library $wp_tasks ) : string
    {
        $template = $wp_tasks->create_template([
            'path' => 'components/mana-symbol.php',
            'vars' => array( 'symbol' => $this )
        ]);
        return $template->get_markup();
    }

    /**
     * Get CSS class string for <img> tag
     */
    public function get_css_class() : string
    {
        return 'mtg-symbol';
    }

    /**
     * Get plaintext key
     */
    public function get_plaintext() : string
    {
        return $this->get_string_prop( 'plaintext' );
    }

    /**
     * Get English phrase (for alt text, et.al.)
     */
    public function get_english_phrase() : string
    {
        return ucfirst( $this->get_string_prop( 'english_phrase' ) );
    }

    /**
     * Get uri to svg image
     */
    public function get_svg_uri() : string
    {
        return $this->get_string_prop( 'svg_uri' );
    }

}   // End of class