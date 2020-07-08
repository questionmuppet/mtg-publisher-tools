<?php
/**
 * Input_Text
 * 
 * Outputs an HTML text input
 */

namespace Mtgtools\Wp_Tasks\Inputs;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Input_Text extends Input
{
    /**
     * Default properties
     */
    protected $defaults = array(
        'size' => 30,
    );

    /**
     * Output HTML
     */
    public function print() : void
    {
        printf(
            '<input type="text" id="%s" name="%s" value="%s" class="%s" placeholder="%s" size="%d" />',
            esc_attr( $this->get_id() ),
            esc_attr( $this->get_name() ),
            esc_attr( $this->get_value() ),
            esc_attr( $this->get_css_class() ),
            esc_attr( $this->get_placeholder() ),
            esc_attr( $this->get_size() )
        );
    }

    /**
     * Get size
     */
    protected function get_size() : int
    {
        return $this->get_prop( 'size' );
    }

}   // End of class