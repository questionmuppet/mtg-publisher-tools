<?php
/**
 * Mtgtools_Dashboard
 * 
 * Creates and displays admin pages
 */

namespace Mtgtools;
use Mtgtools\Abstracts\Module;
use Mtgtools\Dashboard\Dashboard_Tab;
use Mtgtools\Templates\Template;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Dashboard extends Module
{
    /**
     * Dashboard tabs
     */
    private $tabs;

    /**
     * Active tab
     */
    private $active;

    /**
     * ---------------------------------
     *   W O R D P R E S S   H O O K S
     * ---------------------------------
     */

    /**
     * Add WP hooks
     */
    public function add_hooks() : void
    {
        add_action( 'admin_menu',            array( $this, 'create_dashboard' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * Create dashboard page and menu item
     * 
     * @hooked admin_menu
     */
    public function create_dashboard() : void
    {
        add_options_page( 'MTG Publisher Tools', 'MTG Publisher Tools', 'manage_options', MTGTOOLS__ADMIN_SLUG, array( $this, 'display_dashboard' ) );
    }

    /**
     * Enqueue scripts and styles
     * 
     * @hooked admin_enqueue_scripts
     */
    public function enqueue_assets( $hook_suffix ) : void
    {
        if ( 'settings_page_' . MTGTOOLS__ADMIN_SLUG === $hook_suffix )
        {
            $this->get_active_tab()->enqueue_assets( $this->mtgtools() );
        }
    }

    /**
     * Display settings page template
     */
    public function display_dashboard() : void
    {
        $template = new Template([
            'path' => 'dashboard/dashboard.php',
            'vars' => [
                'Mtgtools_Dashboard' => $this
            ],
        ]);
        $template->include();
    }

    /**
     * ---------------------
     *   D A S H   T A B S
     * ---------------------
     */

    /**
     * Include data table template
     */
    public function include_data_table() : void
    {
        $template = new Template([
            'path' => 'dashboard/table.php',
            'vars' => [
                'table_data' => $this->get_active_tab()->get_table_data()
            ],
        ]);
        $template->include();
    }

    /**
     * Get tab url
     */
    public function get_tab_url( string $key ) : string
    {
        return $this->get_tab( $key )->get_href();
    }

    /**
     * Get active settings page tab
     */
    public function get_active_tab() : Dashboard_Tab
    {
        if ( !isset( $this->active ) )
        {
            $tab = sanitize_text_field( $_GET['tab'] ?? '' );
            $tab = $this->is_valid_tab( $tab ) ? $tab : $this->get_default_tab();
            $this->active = $this->get_tab( $tab );
        }
        return $this->active;
    }

    /**
     * Get tab by key
     */
    private function get_tab( string $key ) : Dashboard_Tab
    {
        if ( !$this->is_valid_tab( $key ) )
        {
            throw new \OutOfRangeException( get_called_class() . " tried to retrieve an invalid dashboard tab. No tab is defined for key '{$key}'." );
        }
        return $this->get_tabs()[ $key ];
    }

    /**
     * Check if tab keyname is valid
     */
    private function is_valid_tab( string $tab ) : bool
    {
        return array_key_exists( $tab, $this->get_tabs() );
    }

    /**
     * Get keyname of the default (first) tab
     */
    private function get_default_tab() : string
    {
        return array_keys( $this->get_tabs() )[0];
    }

    /**
     * Get settings page tabs
     * 
     * @return Dashboard_Tab[]
     */
    public function get_tabs() : array
    {
        if ( !isset( $this->tabs ) )
        {
            $tabs = [];
            foreach ( $this->get_tab_defs() as $id => $params )
            {
                $params['id'] = $id;
                $tabs[ $id ] = new Dashboard_Tab( $params );
            }
            $this->tabs = $tabs;
        }
        return $this->tabs;
    }

    /**
     * Get tab definitions
     */
    private function get_tab_defs() : array
    {
        return apply_filters( 'mtgtools_dashboard_tab_definitions', [
            'settings' => [],
        ]);
    }

}   // End of class