<?php
/**
 * Settings_Section
 * 
 * Section for grouping options on settings pages
 */

namespace Mtgtools\Wp_Tasks\Options;
use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'ABSPATH' ) or die("Don't mess with it!");

class Settings_Section extends Data
{
    /**
     * Required properties
     */
    protected $required = array( 'id', 'page' );

    /**
     * Defaults
     */
    protected $defaults = array(
        'title' => null,
        'description' => '',
        'options' => [],
    );

    /**
     * Register section for WordPress settings pages
     */
    public function wp_register() : void
    {
        add_settings_section(
            $this->get_id(),
            $this->get_title(),
            array( $this, 'print_description' ),
            $this->get_page()
        );
        $this->register_fields();
    }
    
    /**
     * Register settings fields
     */
    private function register_fields() : void
    {
        foreach ( $this->get_plugin_options() as $option )
        {
            $this->add_field( $option );
        }
    }

    /**
     * Add an option as a setting field
     */
    private function add_field( Plugin_Option $option ) : void
    {
        $option->wp_register( $this->get_page() );
        add_settings_field(
            $option->get_id(),
            $option->get_label(),
            array( $option, 'print_input' ),
            $this->get_page(),
            $this->get_id(),
            array( 'label_for' => $option->get_id() )
        );
    }

    /**
     * Print section description
     */
    public function print_description() : void
    {
        if ( $this->has_description() )
        {
            printf(
                '<p>%s</p>',
                wp_kses_post( $this->get_description() )
            );
        }
    }

    /**
     * Check if section has a description
     */
    private function has_description() : bool
    {
        return !empty( $this->get_description() );
    }

    /**
     * Get section description
     */
    private function get_description() : string
    {
        return $this->get_prop( 'description' );
    }

    /**
     * Get page
     */
    private function get_page() : string
    {
        return sprintf(
            '%s_%s',
            MTGTOOLS__ADMIN_SLUG,
            $this->get_prop( 'page' )
        );
    }

    /**
     * Get title
     */
    private function get_title() : string
    {
        return $this->get_prop( 'title' ) ?? $this->get_id();
    }

    /**
     * Get id
     */
    public function get_id() : string
    {
        return $this->get_prop( 'id' );
    }

    /**
     * Get plugin options contained in section
     * 
     * @return Plugin_Option[]
     */
    private function get_plugin_options() : array
    {
        return $this->get_prop( 'options' );
    }

}   // End of class