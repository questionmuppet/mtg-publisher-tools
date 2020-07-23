<?php
/**
 * Mtgtools_Settings
 * 
 * Module that controls plugin options and settings pages
 */

namespace Mtgtools;

use Mtgtools\Abstracts\Module;
use Mtgtools\Wp_Tasks\Options\Options_Manager;
use Mtgtools\Wp_Tasks\Options\Settings_Section;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Settings extends Module
{
    /**
     * Sections
     */
    private $sections;

    /**
     * Section definitions
     */
    private $section_defs;

    /**
     * Option manager
     */
    private $options_manager;

    /**
     * Constructor
     */
    public function __construct( Options_Manager $options_manager, $plugin )
    {
        $this->options_manager = $options_manager;
        $this->section_defs = $this->get_section_definitions();
        parent::__construct( $plugin );
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
            $params['options'] = $this->get_options_for_section( $params['options'] );
            $sections[] = new Settings_Section( $params );
        }
        return $sections;
    }

    /**
     * -------------------------
     *   D E F I N I T I O N S
     * -------------------------
     */
    
    /**
     * Add a setting section definition
     */
    public function add_setting_section( array $params ) : void
    {
        $this->section_defs[] = $params;
    }

    /**
     * Get section definitions
     */
    private function get_section_definitions() : array
    {
        return [
            [
                'id' => 'mtgtools_card_images',
                'title' => 'Card images',
                'page' => 'settings',
                'description' => 'Settings to control Magic card images in popups and inline content.',
                'options' => [
                    'inline_image_type',
                    'lazy_fetch_images',
                    'image_cache_period_in_seconds',
                    'popup_tooltip_location',
                    'default_language',
                ],
            ],
            [
                'id' => 'mtgtools_updates',
                'title' => 'Automated updates',
                'page' => 'settings',
                'description' => 'Settings to control automated updates to Magic card data.',
                'options' => [
                    'check_for_updates',
                    'show_update_notices',
                ],
            ],
        ];
    }

    /**
     * -----------------
     *   O P T I O N S
     * -----------------
     */

    /**
     * Get plugin options assigned to a section
     */
    private function get_options_for_section( array $option_keys ) : array
    {
        $opts = [];
        foreach ( $option_keys as $key )
        {
            $opts[] = $this->options_manager()->get_option( $key );
        }
        return $opts;
    }

    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * Get option manager
     */
    private function options_manager() : Options_Manager
    {
        return $this->options_manager;
    }

}   // End of class