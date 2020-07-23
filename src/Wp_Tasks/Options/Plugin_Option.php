<?php
/**
 * Plugin_Option
 * 
 * Abstract class for plugin options appearing on settings page
 */

namespace Mtgtools\Wp_Tasks\Options;
use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Plugin_Option extends Data
{
    /**
     * Required properties
     */
    protected $required = [
        'id',
    ];

    /**
     * Default properties
     */
    protected $abstract_defaults = [
        'default_value' => '',
        'label' => null,
        'input_args' => [],
        'sanitization' => null,
        'autoload' => true,
    ];

    /**
     * -----------------------
     *   U S E R   I N P U T
     * -----------------------
     */

    /**
     * Print HTML input element
     */
    abstract public function print_input() : void;
    
    /**
     * Get arguments for HTML input
     */
    protected function get_input_args() : array
    {
        return array_replace(
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
        update_option(
            $this->get_option_name(),
            $this->sanitize_save_value( $value ),
            $this->is_autoloaded()
        );
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
     * Apply sanitization method before saving in db
     */
    public function sanitize_save_value( $value )
    {
        $method = $this->get_sanitization_method();
        $sanitized = call_user_func( $method, $value );

        /**
         * Allow event triggers on option update
         * 
         * @param mixed $sanitized          New value being saved
         * @param mixed $value              Previous value
         * @param Plugin_Option $option     The full option object
         */
        do_action( "mtgtools_save_option_{$this->get_id()}", $sanitized, $this->get_value(), $this );

        return $sanitized;
    }

    /**
     * Get the sanitization method
     * 
     * @return callable Callback passed to constructor or defined in child class
     */
    private function get_sanitization_method() : callable
    {
        return $this->get_prop( 'sanitization' ) ?? array( $this, 'sanitize' );
    }

    /**
     * Sanitize value before saving
     * 
     * @param mixed $value Value to be saved in db
     * @return mixed Value sanitized for the appropriate type
     */
    abstract protected function sanitize( $value );

    /**
     * -------------
     *   P R O P S
     * -------------
     */
    
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
     * Get label
     */
    public function get_label() : string
    {
        return $this->get_prop( 'label' ) ?? ucfirst( $this->get_id() );
    }

    /**
     * Get name for wp_options table
     */
    public function get_option_name() : string
    {
        return sprintf( "%s_%s", MTGTOOLS__ADMIN_SLUG, $this->get_id() );
    }
    
    /**
     * Get unique identifier
     */
    public function get_id() : string
    {
        return $this->get_prop( 'id' );
    }

    /**
     * Check if option should be autoloaded
     */
    private function is_autoloaded() : bool
    {
        return $this->get_prop( 'autoload' );
    }

}   // End of class