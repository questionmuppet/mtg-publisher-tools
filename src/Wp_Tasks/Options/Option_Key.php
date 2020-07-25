<?php
/**
 * Option_Key
 * 
 * Single plugin option with key value
 */

namespace Mtgtools\Wp_Tasks\Options;
use Mtgtools\Wp_Tasks\Inputs\Input_Text;

// Exit if accessed directly
defined( 'ABSPATH' ) or die("Don't mess with it!");

class Option_Key extends Plugin_Option
{
    /**
     * Sanitization callback
     */
    protected function sanitize( $value ) : string
    {
        return preg_replace( '/[^a-zA-Z0-9_\-.]/', '', sanitize_text_field( $value ) );
    }

    /**
     * Echo input HTML
     */
    public function print_input() : void
    {
        $input = new Input_Text(
            array_merge(
                [
                    'pattern' => '^[a-zA-Z0-9_\-.]+$',
                ],
                $this->get_input_args()
            )
        );
        $input->print();
    }

}   // End of class