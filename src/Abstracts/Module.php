<?php
/**
 * Module
 * 
 * Abstract class for plugin submodules
 */

namespace Mtgtools\Abstracts;
use Mtgtools\Mtgtools_Plugin;
use Mtgtools\Wp_Task_Library;
use Mtgtools\Wp_Tasks\Templates\Template;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Module
{
    /**
     * Plugin instance
     */
    private $plugin;

    /**
     * Admin-post handlers
     */
    private $handlers = [];

    /**
     * Constructor
     */
    public function __construct( Mtgtools_Plugin $plugin )
    {
        $this->plugin = $plugin;
    }

    /**
     * ---------------------------
     *   P L U G I N   T A S K S
     * ---------------------------
     */

    /**
     * Get dashboard url
     */
    protected function get_dashboard_url( string $tab ) : string
    {
        return $this->plugin()->dashboard()->get_tab_url( $tab );
    }

    /**
     * Get the value of a registered plugin option
     * 
     * @return mixed
     */
    protected function get_plugin_option( string $key )
    {
        return $this->plugin()->settings()->get_plugin_option( $key )->get_value();
    }

    /**
     * Update a registered plugin option
     * 
     * @param mixed $value
     */
    protected function update_plugin_option( string $key, $value ) : void
    {
        $this->plugin()->settings()->get_plugin_option( $key )->update( $value );
    }

    /**
     * -------------------
     *   W P   T A S K S
     * -------------------
     */

    /**
     * Enqueue a CSS style
     */
    protected function add_style( array $params ) : void
    {
        $asset = $this->wp_tasks()->create_style( $params );
        $asset->enqueue();
    }

    /**
     * Enqueue a JS script
     */
    protected function add_script( array $params ) : void
    {
        $asset = $this->wp_tasks()->create_script( $params );
        $asset->enqueue();
    }

    /**
     * Print an admin notice
     */
    protected function print_admin_notice( array $params ) : void
    {
        $notice = $this->wp_tasks()->create_admin_notice( $params );
        $notice->print();
    }

    /**
     * Get markup from a themeable template
     */
    protected function get_template_markup( array $params ) : string
    {
        $template = $this->wp_tasks()->create_template( $params );
        return $template->get_markup();
    }

    /**
     * Print a themeable template
     */
    protected function print_template( array $params ) : void
    {
        $template = $this->wp_tasks()->create_template( $params );
        $template->include();
    }

    /**
     * Register admin-post handlers
     */
    protected function register_post_handlers( array $defs ) : void
    {
        foreach ( $defs as $params )
        {
            $handler = $this->wp_tasks()->create_post_handler( $params );
            $handler->add_hooks();
            $this->handlers[ $handler->get_action() ] = $handler;
        }
    }

    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * Get WP Tasks library
     */
    final protected function wp_tasks() : Wp_Task_Library
    {
        return $this->plugin()->wp_tasks();
    }

    /**
     * Get plugin instance
     */
    final protected function plugin() : Mtgtools_Plugin
    {
        return $this->plugin;
    }

}   // End of class