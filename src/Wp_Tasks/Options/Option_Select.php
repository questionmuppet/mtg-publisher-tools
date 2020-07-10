<?php
/**
 * Option_Select
 * 
 * Single plugin option with select input
 */

namespace Mtgtools\Wp_Tasks\Options;
use Mtgtools\Wp_Tasks\Inputs\Input_Select;

// Exit if accessed directly
defined( 'ABSPATH' ) or die("Don't mess with it!");

class Option_Select extends Option
{
    /**
     * Default properties
     */
    protected $defaults = array(
        'placeholder' => 'Select an option...',
        'options' => [],
        'options_callback' => null,
    );

    /**
     * Sanitization callback
     */
    public function sanitize( $value )
    {
        $sanitized = sanitize_text_field( $value );
        return $this->is_valid_option( $sanitized ) ? $sanitized : $this->get_default_value();
    }
    
    /**
     * Echo HTML input element
     */
    public function print_input() : void
    {
        $input = new Input_Select(
            array_merge(
                [
                    'options' => $this->get_options_with_placeholder(),
                ],
                $this->get_input_args()
            )
        );
        $input->print();
    }

    /**
     * Get options including default placeholder
     */
    private function get_options_with_placeholder() : array
    {
        return array_merge(
            [ $this->get_default_value() => $this->get_placeholder() ],
            $this->get_options()
        );
    }

    /**
     * Check for valid option
     */
    private function is_valid_option( string $key ) : bool
    {
        return array_key_exists( $key, $this->get_options() );
    }

    /**
     * Get options for select input
     */
    private function get_options() : array
    {
        $callback = $this->get_prop( 'options_callback' );
        return is_callable( $callback ) ? call_user_func( $callback ) : $this->get_prop( 'options' );
    }

    /**
     * Get placeholder for select (i.e. "choose an option...")
     */
    private function get_placeholder() : string
    {
        return $this->get_prop( 'placeholder' );
    }

}   // End of class