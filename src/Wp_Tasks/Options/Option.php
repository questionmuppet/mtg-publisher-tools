<?php
/**
 * Option
 * 
 * Abstract class for plugin options appearing on settings page
 */

namespace Mtgtools\Wp_Tasks\Options;
use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Option extends Data
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
    protected $abstract_defaults = [
        'default_value' => '',
        'section' => '',
        'label' => null,
        'input_args' => [],
        'sanitization' => null,
    ];

    /**
     * ---------------------------
     *   S E T T I N G S   A P I
     * ---------------------------
     */

    /**
     * Register option for WordPress setting screens
     */
    public function wp_register() : void
    {
        register_setting(
            $this->get_page(),
            $this->get_option_name(),
            array( 'sanitize_callback' => array( $this, 'sanitize_override' ) )
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
     * Get arguments for HTML input
     */
    protected function get_input_args() : array
    {
        return array_merge(
            [
                'id' => $this->get_id(),
                'name' => $this->get_option_name(),
                'value' => $this->get_value(),
            ],
            $this->get_prop( 'input_args' )
        );
    }

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
        update_option( $this->get_option_name(), $this->sanitize_override( $value ) );
    }

    /**
     * Delete from db
     */
    public function delete() : void
    {
        delete_option( $this->get_option_name() );
    }

    /**
     * ---------------------------
     *   S A N I T I Z A T I O N
     * ---------------------------
     */

    /**
     * Override default sanitization if external method provided
     */
    public function sanitize_override( $value )
    {
        $sanitized = $this->has_external_sanitization()
            ? call_user_func( $this->get_external_sanitization_callback(), $value )
            : $this->sanitize( $value );
        
        /**
         * Allow filtering of final value
         * 
         * @param mixed $value      Value of the option before saving
         * @param Option $option    The option object
         */
        return apply_filters( "mtgtools_save_option_{$this->get_id()}", $sanitized, $this->get_value(), $this );
    }

    /**
     * Sanitize value before saving
     * 
     * @param mixed $value Value to be saved in db
     * @return mixed Value sanitized for the appropriate type
     */
    abstract public function sanitize( $value );

    /**
     * Check for external sanitization callback
     */
    private function has_external_sanitization() : bool
    {
        return $this->prop_isset( 'sanitization' );
    }

    /**
     * Get external sanitization callback
     */
    private function get_external_sanitization_callback() : callable
    {
        return $this->get_prop( 'sanitization' );
    }

    /**
     * -------------
     *   P R O P S
     * -------------
     */

    /**
     * Get label
     */
    protected function get_label() : string
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
    protected function get_default_value()
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