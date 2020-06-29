<?php
/**
 * Admin_Notice
 * 
 * Single admin notice for display on WP admin pages
 */

namespace Mtgtools\Tasks\Notices;
use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Admin_Notice extends Data
{
    /**
     * Required properties
     */
    protected $required = array(
        'message',
    );
    
    /**
     * Default properties
     */
    protected $defaults = array(
        'type'        => 'info',
        'title'       => '',
        'dismissible' => true,
    );

    /**
     * Echo notice HTML
     */
    public function print() : void
    {
        printf(
            '<div class="%s">%s<p>%s</p></div>',
            esc_attr( $this->get_class() ),
            $this->get_title_html(),
            wp_kses_post( $this->get_message() )
        );
    }

    /**
     * Get title HTML
     */
    private function get_title_html() : string
    {
        $title = $this->get_title();
        return empty( $title )
            ? ''
            : sprintf( '<h2>%s</h2>', esc_html( $title ) );
    }

    /**
     * Get CSS class string
     */
    private function get_class() : string
    {
        $classes = array_filter([
            'notice',
            sprintf( 'notice-%s', $this->get_type() ),
            $this->is_dismissible() ? 'is-dismissible' : '',
        ]);
        return implode( ' ', $classes );
    }

    /**
     * Get message HTML
     */
    private function get_message() : string
    {
        return $this->get_prop( 'message' );
    }

    /**
     * Get notice type
     */
    private function get_type() : string
    {
        return $this->get_prop( 'type' );
    }

    /**
     * Get title
     */
    private function get_title() : string
    {
        return $this->get_prop( 'title' );
    }

    /**
     * Check if user can close notice (with x button)
     */
    private function is_dismissible() : bool
    {
        return boolval( $this->get_prop( 'dismissible' ) );
    }

}   // End of class