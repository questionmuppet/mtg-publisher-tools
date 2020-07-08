<?php
/**
 * Plugin_Setting
 * 
 * Abstract class that interfaces with WordPress options and settings APIs
 */

namespace Mtgtools\Wp_Tasks\Settings;

use Mtgtools\Abstracts\Data;
use Mtgtools\Wp_Tasks\Inputs;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Plugin_Setting extends Data
{
    /**
     * Required properties
     */
    protected $required = [
        'id',
        'page',
    ];

    /**
     * Default properties
     */
    protected $defaults = [
        'default_value' => '',
        'section' => '',
        'label' => null,
        'input_args' => [],
    ];

    /**
     * ---------------------------
     *   S E T T I N G S   A P I
     * ---------------------------
     */

    /**
     * Register setting for WordPress admin screens
     */
    public function wp_register() : void
    {
        register_setting(
            $this->get_page(),
            $this->get_option_name(),
            array( 'sanitize_callback' => array( $this, 'sanitize' ) )
        );
        if ( $this->has_section() )
        {
            add_settings_field(
                $this->get_id(),
                $this->get_label(),
                array( $this, 'print_input' ),
                $this->get_page(),
                $this->get_section(),
                array( 'label_for' => $this->get_id() )
            );
        }
    }

    /**
     * Print HTML input element
     */
    abstract public function print_input() : void;

    /**
     * -----------
     *   C R U D
     * -----------
     */

    /**
     * Get current value of setting in db
     * 
     * @return mixed Value of option in database, default value if not found
     */
    public function get_value()
    {
        return get_option( $this->get_option_name(), $this->get_default_value() );
    }

    /**
     * Add option to db with default value
     */
    public function add_to_db() : void
    {
        $this->update( $this->get_default_value() );
    }

    /**
     * Update option value in db
     * 
     * @param mixed $value Value to set option to
     */
    public function update( $value ) : void
    {
        update_option( $this->get_option_name(), $this->sanitize( $value ) );
    }

    /**
     * Delete from db
     */
    public function delete() : void
    {
        delete_option( $this->get_option_name() );
    }

    /**
     * Sanitize value before saving
     * 
     * @param mixed $value Value to be saved in db
     * @return mixed Value sanitized for the appropriate type
     */
    abstract public function sanitize( $value );

    /**
     * -------------
     *   P R O P S
     * -------------
     */

    /**
     * Get optional arguments for HTML input
     */
    protected function get_input_args() : array
    {
        return $this->get_prop( 'input_args' );
    }

    /**
     * Get label
     */
    protected function get_label()
    {
        return $this->get_prop( 'label' ) ?? ucfirst( $this->get_id() );
    }
    
    /**
     * Check if setting is assigned to an admin section
     */
    private function has_section() : bool
    {
        return !empty( $this->get_section() );
    }
    
    /**
     * Get admin section name
     */
    private function get_section() : string
    {
        return $this->get_prop( 'section' );
    }

    /**
     * Get page name
     */
    private function get_page() : string
    {
        return $this->add_prefix( $this->get_prop( 'page' ) );
    }

    /**
     * Get name for wp_options table
     */
    private function get_option_name() : string
    {
        return $this->add_prefix( $this->get_id() );
    }
    
    /**
     * Get unique identifier
     */
    public function get_id() : string
    {
        return $this->get_prop( 'id' );
    }
    
    /**
     * Get default value
     * 
     * @return mixed
     */
    private function get_default_value()
    {
        return $this->get_prop( 'default_value' );
    }

    /**
     * Add prefix to keyname
     * 
     * @return string Keyname with unique plugin prefix appended
     */
    private function add_prefix( string $key ) : string
    {
        return sprintf( "%s_%s", MTGTOOLS__ADMIN_SLUG, $key );
    }

}   // End of class