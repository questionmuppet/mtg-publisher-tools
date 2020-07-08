<?php
/**
 * Input_Number
 * 
 * Outputs an HTML number input
 */

namespace Mtgtools\Wp_Tasks\Inputs;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Input_Number extends Input
{
    /**
     * Default properties
     */
    protected $defaults = array(
        'min'   => 0,
        'max'   => '',
        'step'  => 1,
        'units' => '',
    );

    /**
     * Output HTML
     */
    public function print() : void
    {
        printf(
            '<input type="number" id="%s" name="%s" value="%s" class="%s" placeholder="%s" min="%s" max="%s" step="%s" />',
            esc_attr( $this->get_id() ),
            esc_attr( $this->get_name() ),
            esc_attr( $this->get_value() ),
            esc_attr( $this->get_css_class() ),
            esc_attr( $this->get_placeholder() ),
            esc_attr( $this->get_min() ),
            esc_attr( $this->get_max() ),
            esc_attr( $this->get_step() )
        );
        $this->print_units_label();
    }

    /**
     * Print units label, if any
     */
    private function print_units_label() : void
    {
        $units = $this->get_units();
        if ( !empty( $units ) )
        {
            printf(
                '<span class="input-units">%s</span>',
                esc_html( $units )
            );
        }
    }

    /**
     * Get min value
     */
    private function get_min() : string
    {
        return strval( $this->get_prop( 'min' ) );
    }

    /**
     * Get max value
     */
    private function get_max() : string
    {
        return strval( $this->get_prop( 'max' ) );
    }

    /**
     * Get step increment
     */
    private function get_step() : string
    {
        return strval( $this->get_prop( 'step' ) );
    }

    /**
     * Get units label
     */
    private function get_units() : string
    {
        return $this->get_prop( 'units' );
    }

}   // End of class