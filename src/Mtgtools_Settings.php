<?php
/**
 * Mtgtools_Settings
 * 
 * Module that controls plugin options and settings screens
 */

namespace Mtgtools;

use Mtgtools\Abstracts\Module;
use Mtgtools\Settings\Setting_Factory;
use Mtgtools\Settings\Section_Factory;
use Mtgtools\Settings\Plugin_Setting;
use Mtgtools\Settings\Settings_Section;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Settings extends Module
{
    /**
     * Setting definitions
     */
    private $setting_defs = [];
    
    /**
     * Settings
     */
    private $settings = [];

    /**
     * Factories
     */
    private $factories = [];

    /**
     * Add WordPress hooks
     */
    public function add_hooks() : void
    {
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Register settings for WP admin pages
     */
    public function register_settings() : void
    {

    }

    /**
     * Get the current value of a plugin setting
     * 
     * @param string $key   Unprefixed option name
     * @return mixed        Value in db, default value if not found
     */
    public function get_setting_value( string $key )
    {
        return $this->get_setting( $key )->retrieve();
    }

    /**
     * -------------------
     *   S E T T I N G S
     * -------------------
     */

    /**
     * Get plugin setting
     */
    private function get_setting( string $key ) : Plugin_Setting
    {
        if ( !isset( $this->settings[ $key ] ) )
        {
            $this->settings[ $key ] = $this->create_setting( $key );
        }
        return $this->settings[ $key ];
    }
    
    /**
     * Create plugin setting from defined params
     */
    private function create_setting( string $key ) : Plugin_Setting
    {
        if ( !$this->setting_defined( $key ) )
        {
            throw new \OutOfRangeException( get_called_class() . " tried to retrieve an undefined plugin setting. No setting registered for key '{$key}'." );
        }
        return $this->setting_factory()->create_setting(
            $this->setting_defs[ $key ]
        );
    }

    /**
     * Check if a plugin setting is defined
     */
    private function setting_defined( string $key ) : bool
    {
        return array_key_exists( $key, $this->setting_defs );
    }

    /**
     * ---------------------
     *   F A C T O R I E S
     * ---------------------
     */

    /**
     * Get setting factory
     */
    protected function setting_factory() : Setting_Factory
    {
        if ( !isset( $this->factories['setting'] ) )
        {
            $this->factories['setting'] = new Setting_Factory();
        }
        return $this->factories['setting'];
    }

    /**
     * Get section factory
     */
    protected function section_factory() : Section_Factory
    {
        if ( !isset( $this->factories['section'] ) )
        {
            $this->factories['section'] = new Section_Factory();
        }
        return $this->factories['section'];
    }

}   // End of class