<?php
/**
 * Option_Checkbox
 * 
 * Plugin option with boolean checkbox input
 */

namespace Mtgtools\Wp_Tasks\Options;
use Mtgtools\Wp_Tasks\Inputs\Input_Checkbox;

// Exit if accessed directly
defined( 'ABSPATH' ) or die("Don't mess with it!");

class Option_Checkbox extends Plugin_Option
{
    /**
     * Default properties
     */
    protected $defaults = array(
        'default_value'  => false,
    );

    /**
     * Sanitization callback
     */
    protected function sanitize( $value ) : bool
    {
        return boolval( $value );
    }

    /**
     * Echo HTML input element
     */
    public function print_input() : void
    {
        $input = new Input_Checkbox( $this->get_input_args() );
        $input->print();
    }

}   // End of class