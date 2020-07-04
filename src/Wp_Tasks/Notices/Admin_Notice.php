<?php
/**
 * Admin_Notice
 * 
 * Generates an admin notice for display on WP admin pages
 */

namespace Mtgtools\Wp_Tasks\Notices;
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
        'p_wrap'      => true,
        'buttons'     => [],
    );

    /**
     * -------------------------
     *   H T M L   O U T P U T
     * -------------------------
     */

    /**
     * Get notice HTML as string
     */
    public function get_markup() : string
    {
        ob_start();
        $this->print();
        return ob_get_clean();
    }

    /**
     * Echo notice HTML
     */
    public function print() : void
    {
        printf(
            '<div class="%s">%s%s%s</div>',
            esc_attr( $this->get_class() ),
            $this->get_title_html(),                    // Sanitized below
            wp_kses_post( $this->get_message() ),
            $this->get_buttons_row()                    // Sanitized below
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
     * -----------------
     *   M E S S A G E
     * -----------------
     */

    /**
     * Get message HTML
     */
    private function get_message() : string
    {
        $message = $this->get_prop( 'message' );
        return $this->is_p_wrapped()
            ? $this->add_p_wrap( $message )
            : $message;
    }

    /**
     * Enclose message in <p> tags
     */
    private function add_p_wrap( string $message ) : string
    {
        return sprintf( "<p>%s</p>", $message );
    }

    /**
     * -----------------
     *   B U T T O N S
     * -----------------
     */

    /**
     * Get HTML for buttons row
     */
    private function get_buttons_row() : string
    {
        return $this->has_buttons()
            ? sprintf( "<p>%s</p>", implode( ' ', $this->get_buttons() ) )
            : '';
    }
    
    /**
     * Get buttons HTML
     * 
     * @return array Button definitions converted into sanitized <a> tags
     */
    private function get_buttons() : array
    {
        $buttons = [];
        foreach ( $this->get_button_defs() as $index => $args )
        {
            $buttons[] = $this->get_button_html(
                $args,
                $this->is_first_button_key( $index )
            );
        }
        return $buttons;
    }
    
    /**
     * Get HTML for a single button
     * 
     * @param array $args       Button arguements passed in the Notice constructor
     * @param bool $is_first    Whether or not button is the first in its series
     */
    private function get_button_html( array $args, bool $is_first = false ) : string
    {
        $args = array_replace([
            'label' => '',
            'href' => '',
            'primary' => null,
        ], $args );

        // Allow override of primary class in button defs
        $is_primary = $args['primary'] ?? $is_first;

        return sprintf(
            '<a class="%s" href="%s">%s</a>',
            esc_attr( $this->get_button_css_class( $is_primary ) ),
            esc_url( $args['href'] ),
            esc_html( $args['label'] )
        );
    }

    /**
     * Get CSS class for a button
     */
    private function get_button_css_class( bool $is_primary = false ) : string
    {
        $classes = array_filter([
            'button',
            $is_primary ? 'button-primary' : '',
        ]);
        return implode( ' ', $classes );
    }

    /**
     * -------------
     *   P R O P S
     * -------------
     */

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

    /**
     * Check whether to wrap the message in <p> tags
     */
    private function is_p_wrapped() : bool
    {
        return boolval( $this->get_prop( 'p_wrap' ) );
    }
    
    /**
     * Check for button definitions
     */
    private function has_buttons() : bool
    {
        return count( $this->get_button_defs() );
    }

    /**
     * Check array key against first button definition
     * 
     * @param mixed $key
     */
    private function is_first_button_key( $key ) : bool
    {
        return $key === array_key_first( $this->get_button_defs() );
    }

    /**
     * Get defined button attributes
     */
    private function get_button_defs() : array
    {
        return $this->get_prop( 'buttons' );
    }

}   // End of class