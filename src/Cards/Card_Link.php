<?php
/**
 * Card_Link
 * 
 * Generates HTML for a Magic card popup link
 */

namespace Mtgtools\Cards;

use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Card_Link extends Data
{
    /**
     * Required properties
     */
    protected $required = [
        'filters',
        'content',
    ];

    /**
     * Default properties
     */
    protected $defaults = [
        'href' => '#',
    ];

    /**
     * Get HTML markup as string
     */
    public function get_markup() : string
    {
        return sprintf(
            '<a href="%s" class="%s" %s>%s</a>',
            esc_url( $this->get_href() ),
            esc_attr( $this->get_css_class() ),
            $this->generate_data_attributes(),
            wp_kses_post( $this->get_content() )
        );
    }

    /**
     * Get CSS class string
     */
    private function get_css_class() : string
    {
        return implode( ' ', $this->get_link_classes() );
    }

    /**
     * Get link classes
     */
    private function get_link_classes() : array
    {
        $classes = array_filter([
            'mtgtools-card-link',
        ]);

        /**
         * Allow for third-party classes
         * 
         * @param array $filters    User-provided search filters in shortcode
         * @param string $content   Interior link content
         */
        return apply_filters( 'mtgtools_card_link_classes', $classes, $this->get_filters(), $this->get_content() );
    }

    /**
     * Generate data-attributes
     * 
     * @return string Data-attributes for each filter, sanitized for inclusion in HTML element
     */
    private function generate_data_attributes() : string
    {
        $attrs = [];
        foreach ( $this->get_filters() as $key => $value )
        {
            $attrs[] = sprintf(
                'data-%s="%s"',
                sanitize_key( $key ),
                esc_attr( $value )
            );
        }
        return implode( ' ', $attrs );
    }

    /**
     * -------------
     *   P R O P S
     * -------------
     */

    /**
     * Get card search filters
     */
    public function get_filters() : array
    {
        return array_replace(
            [ 'name' => $this->get_content() ],
            $this->get_prop( 'filters' )
        );
    }

    /**
     * Get link content
     */
    private function get_content() : string
    {
        return $this->get_prop( 'content' );
    }
    
    /**
     * Set href
     */
    public function set_href( string $href ) : void
    {
        $this->set_prop( 'href', $href );
    }

    /**
     * Get href
     */
    private function get_href() : string
    {
        return $this->get_prop( 'href' );
    }

}   // End of class