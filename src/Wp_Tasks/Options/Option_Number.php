<?php
/**
 * Option_Number
 * 
 * Single plugin option with number value
 */

namespace Mtgtools\Wp_Tasks\Options;
use Mtgtools\Wp_Tasks\Inputs\Input_Number;

// Exit if accessed directly
defined( 'ABSPATH' ) or die("Don't mess with it!");

class Option_Number extends Option
{
    /**
     * Default properties
     */
    protected $defaults = array(
        'default_value' => 0,
        'min'   => 0,
        'max'   => null,
        'step'  => 1,
    );

    /**
     * Sanitization callback
     */
    public function sanitize( $value )
    {
        $bound = $this->find_min(
            $this->find_max(
                $value
            )
        );
        return $bound - ( $bound % $this->get_step() );
    }

    /**
     * Find max value
     */
    private function find_max( $value )
    {
        return min(
            array_filter([ $value, $this->get_max() ], 'strlen')
        );
    }

    /**
     * Find min value
     */
    private function find_min( $value )
    {
        return max(
            array_filter([ $value, $this->get_min() ], 'strlen')
        );
    }

    /**
     * Echo input HTML
     */
    public function print_input() : void
    {
        $input = new Input_Number(
            array_merge(
                [
                    'min' => $this->get_min(),
                    'max' => $this->get_max(),
                    'step' => $this->get_step(),
                ],
                $this->get_input_args(),
            )
        );
        $input->print();
    }

    /**
     * Get min value
     */
    private function get_min() :? float
    {
        return $this->get_prop( 'min' );
    }

    /**
     * Get max value
     */
    private function get_max() :? float
    {
        return $this->get_prop( 'max' );
    }

    /**
     * Get step increment
     */
    private function get_step() : float
    {
        return $this->get_prop( 'step' );
    }

}   // End of class