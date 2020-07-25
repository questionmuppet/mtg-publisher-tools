<?php
/**
 * Input_Select
 * 
 * Outputs an HTML select input
 */

namespace Mtgtools\Wp_Tasks\Inputs;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Input_Select extends Input
{
    /**
     * Default properties
     */
    protected $defaults = array(
        'options' => [],
    );

    /**
     * Output HTML
     */
    public function print() : void
    {
        printf(
            '<select id="%s" name="%s" value="%s" class="%s">%s</select>',
            esc_attr( $this->get_id() ),
            esc_attr( $this->get_name() ),
            esc_attr( $this->get_value() ),
            esc_attr( $this->get_css_class() ),
            $this->get_options_html()
        );
    }

    /**
     * Get options HTML
     */
    protected function get_options_html() : string
    {
        $html = '';
        foreach ( $this->get_options() as $key => $label )
        {
            $html .= sprintf(
                '<option value="%s"%s>%s</option>',
                esc_attr( $key ),
                $this->is_selected( $key ) ? ' selected' : '',
                esc_html( $label )
            );
        }
        return $html;
    }

    /**
     * Check if an option is selected
     */
    protected function is_selected( string $key ) : bool
    {
        return $this->get_value() === $key;
    }

    /**
     * Get select options
     */
    protected function get_options() : array
    {
        return $this->get_prop( 'options' );
    }

}   // End of class