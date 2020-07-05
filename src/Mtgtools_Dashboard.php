<?php
/**
 * Mtgtools_Dashboard
 * 
 * Creates and displays admin pages
 */

namespace Mtgtools;

use Mtgtools\Abstracts\Module;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab_Factory;
use Mtgtools\Exceptions\Admin_Post\ParameterException;
use Mtgtools\Wp_Tasks\Tables\Table_Data;
use Mtgtools\Wp_Tasks\Templates\Template;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Dashboard extends Module
{
    /**
     * Dashboard tabs
     */
    private $tabs;

    /**
     * Tab definitions
     */
    private $tab_defs = [
        [
            'id' => 'settings',
        ],
    ];

    /**
     * Active tab
     */
    private $active;

    /**
     * Tab factory;
     */
    private $tab_factory;

    /**
     * Constructor
     */
    public function __construct( Dashboard_Tab_Factory $tab_factory, $wp_tasks )
    {
        $this->tab_factory = $tab_factory;
        parent::__construct( $wp_tasks );
    }

    /**
     * Add WP hooks
     */
    public function add_hooks() : void
    {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'admin_menu',            array( $this, 'create_dashboard' ) );

        $this->register_post_handlers([
            [
                'type'      => 'ajax',
                'action'    => 'mtgtools_update_table',
                'callback'  => array( $this, 'update_data_table' ),
                'user_args' => [ 'tab', 'table', 'filter' ],
            ],
        ]);
    }

    /**
     * -----------------
     *   E N Q U E U E
     * -----------------
     */

    /**
     * Enqueue scripts and styles
     * 
     * @hooked admin_enqueue_scripts
     */
    public function enqueue_assets( $hook_suffix ) : void
    {
        if ( 'settings_page_' . MTGTOOLS__ADMIN_SLUG === $hook_suffix )
        {
            foreach ( $this->get_assets() as $asset )
            {
                $asset->enqueue();
            }
        }
    }
    
    /**
     * Get dashboard assets
     * 
     * @return Asset[]
     */
    private function get_assets() : array
    {
        return array_merge(
            [
                $this->wp_tasks()->create_style([
                    'key'  => 'mtgtools-dashboard',
                    'path' => 'dashboard.css',
                ]),
            ],
            $this->get_active_tab()->get_assets()
        );
    }

    /**
     * Enqueue assets for data tables
     */
    private function enqueue_table_assets() : void
    {
        $script = $this->wp_tasks()->create_script([
            'key'  => 'mtgtools-data-table',
            'path' => 'data-tables.js',
            'data' => [
                'mtgtoolsDataTable' => [ 'nonce' => wp_create_nonce('mtgtools_update_table') ]
            ]
        ]);
        $script->enqueue();
    }

    /**
     * ---------------------
     *   D A S H B O A R D
     * ---------------------
     */
    
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
     * Display main dashboard template
     */
    public function display_dashboard() : void
    {
        $this->display_template([
            'path'      => 'dashboard/dashboard.php',
            'themeable' => false,
            'vars'      => [
                'dashboard'  => $this,
            ],
        ]);
    }

    /**
     * Display data table
     */
    public function display_table( string $key ) : void
    {
        $tab = $this->get_active_tab();
        $this->enqueue_table_assets();
        
        $this->display_template([
            'path' => 'dashboard/data-table/table.php',
            'vars' => [
                'active_tab' => $tab,
                'table_data' => $tab->get_table_data( $key )
            ],
        ]);
    }

    /**
     * Display a dashboard template
     */
    private function display_template( array $params ) : void
    {
        $template = $this->wp_tasks()->create_template( $params );
        $template->include();
    }

    /**
     * -----------------------
     *   D A T A   T A B L E
     * -----------------------
     */

    /**
     * Update data table for AJAX call
     * 
     * @throws PostHandlerException
     */
    public function update_data_table( array $args = [] ) : array
    {
        $data = $this->find_table_data( $args['tab'], $args['table'] );
        $data->set_filter( $args['filter'] );
        
        $template = $this->get_table_body_template( $data );

        return [
            'transients' => [ 'tableBody' => $template->get_markup() ]
        ];
    }

    /**
     * Get table data by tab and key
     */
    private function find_table_data( string $tab, string $key ) : Table_Data
    {
        try
        {
            return $this->get_tab( $tab )->get_table_data( $key );
        }
        catch ( \OutOfRangeException $e )
        {
            throw new ParameterException( "Requested an update for an undefined data table. Could not find table '{$key}' for tab '{$tab}'." );
        }
    }

    /**
     * Get table body template
     */
    private function get_table_body_template( Table_Data $data ) : Template
    {
        return $this->wp_tasks()->create_template([
            'path' => 'dashboard/data-table/table-body.php',
            'vars' => [
                'table_data' => $data
            ]
        ]);
    }

    /**
     * -----------------------------------
     *   T E M P L A T E   H E L P E R S
     * -----------------------------------
     */

    /**
     * Print a simple table of static information
     */
    public function print_info_table( array $rows ) : void
    {
        $this->print_template([
            'path' => 'dashboard/components/info-table.php',
            'themeable' => false,
            'vars' => [
                'classes' => [],
                'rows' => $rows
            ]
        ]);
    }

    /**
     * Print form inputs for an admin-post action
     * 
     * @param string $params['action']  Action to submit to admin-post.php
     * @param string $params['label']   Label for the submit button
     * @param bool $params['primary']   Assign WordPress "button-primary" class to button
     */
    public function print_action_inputs( array $params ) : void
    {
        $params = array_replace([
            'action' => '',
            'label' => '',
            'primary' => false,
        ], $params );
        
        $this->print_template([
            'path' => 'dashboard/components/action-inputs.php',
            'themeable' => false,
            'vars' => $params,
        ]);
    }

    /**
     * Print notices for the result of an admin-post action
     * 
     * @param array $actions    Associative array of "action" => [notice_params]
     */
    public function print_action_notices( array $actions ) : void
    {
        $key = $_GET['action'] ?? '';
        if ( array_key_exists( $key, $actions ) )
        {
            $this->print_admin_notice( $actions[$key] );
        }
    }

    /**
     * ---------------------
     *   D A S H   T A B S
     * ---------------------
     */

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
        if ( !$this->tabs_created() )
        {
            /**
             * Allow modules and third-parties to add dashboard tabs
             * 
             * @param Mtgtools_Dashboard $dashboard     Dashboard module; callbacks should use add_tab() method to create their tab.
             * @param array $tab_defs                   Currently defined tabs. Tab order can be controlled using the priority parameter on add_action().
             */
            do_action( 'mtgtools_dashboard_tabs', $this, $this->tab_defs );
            
            $this->tabs = $this->create_tabs();
        }
        return $this->tabs;
    }

    /**
     * Add dashboard tab definition
     */
    public function add_tab( array $params ) : void
    {
        if ( $this->tabs_created() )
        {
            throw new \RuntimeException( "Tried to add a dashboard tab at the wrong time. Custom dashboard tabs should be created using the 'mtgtools_dashboard_tabs' action hook." );
        }
        array_unshift( $this->tab_defs, $params );
    }

    /**
     * Create dashboard tab objects
     */
    private function create_tabs() : array
    {
        $tabs = [];
        foreach ( $this->tab_defs as $params )
        {
            $tab = $this->tab_factory()->create_tab( $params );
            $tabs[ $tab->get_id() ] = $tab;
        }
        unset( $this->tab_defs );
        return $tabs;
    }

    /**
     * Check if tabs have been created
     */
    private function tabs_created() : bool
    {
        return isset( $this->tabs );
    }

    /**
     * Get dashtab factory
     */
    private function tab_factory() : Dashboard_Tab_Factory
    {
        return $this->tab_factory;
    }

}   // End of class