<?php
/**
 * Option_Text
 * 
 * Single plugin option with text value
 */

namespace Mtgtools\Wp_Tasks\Options;
use Mtgtools\Wp_Tasks\Inputs\Input_Text;

// Exit if accessed directly
defined( 'ABSPATH' ) or die("Don't mess with it!");

class Option_Text extends Plugin_Option
{
    /**
     * Sanitization callback
     */
    protected function sanitize( $value ) : string
    {
        return sanitize_text_field( $value );
    }

    /**
     * Echo input HTML
     */
    public function print_input() : void
    {
        $input = new Input_Text( $this->get_input_args() );
        $input->print();
    }

}   // End of class