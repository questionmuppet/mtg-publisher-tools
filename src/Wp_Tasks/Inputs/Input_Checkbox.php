<?php
/**
 * Input_Checkbox
 * 
 * Outputs an HTML checkbox input
 */

namespace Mtgtools\Wp_Tasks\Inputs;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Input_Checkbox extends Input
{
    /**
     * Default properties
     */
    protected $defaults = array(
        'label' => '',
    );

    /**
     * Output HTML
     */
    public function print() : void
    {
        printf(
            '<label><input type="checkbox" id="%s" name="%s" value="1" class="%s" %s/>%s</label>',
            esc_attr( $this->get_id() ),
            esc_attr( $this->get_name() ),
            esc_attr( $this->get_css_class() ),
            checked( $this->get_value(), true, false ),
            esc_html( $this->get_label() )
        );
    }

    /**
     * Get label text
     */
    protected function get_label() : string
    {
        return $this->get_prop( 'label' );
    }

}   // End of class