<?php
/**
 * Module
 * 
 * Abstract class for plugin submodules
 */

namespace Mtgtools\Abstracts;
use Mtgtools\Mtgtools_Plugin;
use Mtgtools\Task_Library;

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
     * Register admin-post handlers
     */
    protected function register_post_handlers( array $defs ) : void
    {
        foreach ( $defs as $params )
        {
            $handler = $this->tasks()->create_post_handler( $params );
            $handler->add_hooks();
            $this->handlers[ $handler->get_action() ] = $handler;
        }
    }

    /**
     * Get task library
     */
    final protected function tasks() : Task_Library
    {
        return $this->mtgtools()->task_library();
    }

    /**
     * Get plugin instance
     */
    final protected function mtgtools() : Mtgtools_Plugin
    {
        return $this->plugin;
    }

}   // End of class