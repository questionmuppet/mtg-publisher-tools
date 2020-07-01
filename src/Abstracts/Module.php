<?php
/**
 * Module
 * 
 * Abstract class for plugin submodules
 */

namespace Mtgtools\Abstracts;
use Mtgtools\Wp_Task_Library;
use Mtgtools\Wp_Tasks\Templates\Template;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Module
{
    /**
     * WP Task library
     */
    private $wp_tasks;

    /**
     * Admin-post handlers
     */
    private $handlers = [];

    /**
     * Constructor
     */
    public function __construct( Wp_Task_Library $wp_tasks )
    {
        $this->wp_tasks = $wp_tasks;
    }

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
        $asset = $this->wp_tasks()->create_admin_notice( $params );
        $asset->print();
    }

    /**
     * Get a themeable template
     */
    protected function get_template( array $params ) : Template
    {
        return $this->wp_tasks()->create_template( $params );
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
     * Get WP Tasks library
     */
    final protected function wp_tasks() : Wp_Task_Library
    {
        return $this->wp_tasks;
    }

}   // End of class