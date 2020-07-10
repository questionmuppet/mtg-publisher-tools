<?php
/**
 * Mtgtools_Settings
 * 
 * Module that controls plugin options and settings pages
 */

namespace Mtgtools;

use Mtgtools\Abstracts\Module;
use Mtgtools\Wp_Tasks\Options\Option;
use Mtgtools\Wp_Tasks\Options\Option_Factory;
use Mtgtools\Wp_Tasks\Options\Settings_Section;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Settings extends Module
{
    /**
     * Section defs
     */
    private $section_defs = [
        [
            'id' => 'mtgtools_updates',
            'title' => 'Automated updates',
            'page' => 'settings',
            'description' => 'Settings to control automated updates to Magic card data.',
        ],
    ];

    /**
     * Sections
     */
    private $sections;

    /**
     * Option defs
     */
    private $option_defs = [
        'check_for_updates' => [
            'page' => 'settings',
            'section' => 'mtgtools_updates',
            'type' => 'checkbox',
            'default_value' => true,
            'label' => 'Update checker',
            'input_args' => [
                'label' => 'Check for updates automatically'
            ]
        ],
        'update_check_period' => [
            'page' => 'settings',
            'section' => 'mtgtools_updates',
            'type' => 'select',
            'label' => 'Frequency',
            'default_value' => '2',
            'options' => [
                '1' => 'Weekly',
                '2' => 'Biweekly',
                '4' => 'Monthly',
                '24' => 'Every 6 months',
            ],
        ],
    ];

    /**
     * Options
     */
    private $options = [];

    /**
     * Option factory
     */
    private $option_factory;

    /**
     * Constructor
     */
    public function __construct( Option_Factory $factory, $wp_tasks )
    {
        $this->option_factory = $factory;
        parent::__construct( $wp_tasks );
    }

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
        foreach ( $this->get_setting_sections() as $section )
        {
            $section->wp_register();
        }
        foreach ( $this->get_all_options() as $option )
        {
            $option->wp_register();
        }
    }

    /**
     * -------------------
     *   S E C T I O N S
     * -------------------
     */

    /**
     * Get setting sections
     * 
     * @return Setting_Section[]
     */
    private function get_setting_sections() : array
    {
        if ( !isset( $this->sections ) )
        {
            $this->sections = $this->create_sections();
        }
        return $this->sections;
    }
    
    /**
     * Create setting sections
     */
    private function create_sections() : array
    {
        $sections = [];
        foreach ( $this->section_defs as $params )
        {
            $section = new Settings_Section( $params );
            $sections[ $section->get_id() ] = $section;
        }
        return $sections;
    }
    
    /**
     * -----------------
     *   O P T I O N S
     * -----------------
     */

    /**
     * Get plugin option
     */
    public function get_plugin_option( string $key ) : Option
    {
        if ( !$this->option_instantiated( $key ) )
        {
            $this->options[ $key ] = $this->create_option( $key );
        }
        return $this->options[ $key ];
    }

    /**
     * Get all plugin options for settings pages
     * 
     * @return Option[]
     */
    private function get_all_options() : array
    {
        foreach ( $this->option_defs as $key => $params )
        {
            if ( !$this->option_instantiated( $key ) )
            {
                $this->options[ $key ] = $this->create_option( $key );
            }
        }
        return $this->options;
    }
    
    /**
     * Create plugin option from defined params
     */
    private function create_option( string $key ) : Option
    {
        if ( !$this->option_exists( $key ) )
        {
            throw new \OutOfRangeException( get_called_class() . " tried to retrieve an undefined plugin option. No option registered for key '{$key}'." );
        }
        $params = $this->option_defs[ $key ];
        $params['id'] = $key;
        return $this->option_factory()->create_option( $params );
    }

    /**
     * Check if option is instantiated
     */
    private function option_instantiated( string $key ) : bool
    {
        return isset( $this->options[ $key ] );
    }

    /**
     * Check if a plugin option is defined
     */
    private function option_exists( string $key ) : bool
    {
        return array_key_exists( $key, $this->option_defs );
    }

    /**
     * -------------------------
     *   D E F I N I T I O N S
     * -------------------------
     */

    /**
     * Add a plugin option definition
     */
    public function add_plugin_option( array $params ) : void
    {
        if ( !isset( $params['id'] ) )
        {
            throw new \DomainException( "Tried to register a plugin option without a valid id." );
        }
        $this->option_defs[ $params['id'] ] = $params;
    }

    /**
     * Add a setting section definition
     */
    public function add_setting_section( array $params ) : void
    {
        $this->section_defs[] = $params;
    }

    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * Get option factory
     */
    private function option_factory() : Option_Factory
    {
        return $this->option_factory;
    }

}   // End of class