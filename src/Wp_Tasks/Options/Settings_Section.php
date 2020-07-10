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

}   // End of class