<?php
/**
 * Input
 * 
 * Abstract object for outputting HTML form inputs
 */

namespace Mtgtools\Wp_Tasks\Inputs;
use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Input extends Data
{
    /**
     * Universal defaults
     */
    protected $abstract_defaults = array(
        'name'        => null,
        'id'          => '',
        'classes'     => [],
        'value'       => '',
        'placeholder' => '',
    );

    /**
     * Get markup as string
     */
    public function get_markup() : string
    {
        ob_start();
        $this->print();
        return ob_get_clean();
    }

    /**
     * Output HTML
     */
    abstract public function print() : void;

    /**
     * Get name
     */
    protected function get_name() : string
    {
        return $this->get_prop( 'name' ) ?? $this->get_id();
    }

    /**
     * Get id
     */
    protected function get_id() : string
    {
        return $this->get_prop( 'id' );
    }

    /**
     * Get CSS class string
     */
    protected function get_css_class() : string
    {
        return implode( ' ', $this->get_all_classes() );
    }
    
    /**
     * Get all CSS classes as an array
     */
    private function get_all_classes() : array
    {
        return array_merge(
            $this->get_prop( 'classes' ),
            [
                sprintf( "%s-dashboard-input", MTGTOOLS__ADMIN_SLUG )
            ]
        );
    }
    
    /**
     * Get value
     * 
     * @return mixed
     */
    protected function get_value()
    {
        return $this->get_prop( 'value' );
    }

    /**
     * Get placeholder
     */
    protected function get_placeholder() : string
    {
        return $this->get_prop( 'placeholder' );
    }

}   // End of class