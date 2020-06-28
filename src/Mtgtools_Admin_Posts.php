<?php
/**
 * Mtgtools_Admin_Posts
 * 
 * Handles WordPress admin posts
 */

namespace Mtgtools;
use Mtgtools\Abstracts\Module;
use Mtgtools\Admin_Post\Post_Handler_Factory;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Admin_Posts extends Module
{
    /**
     * Handler definitions
     */
    private $defs = [];

    /**
     * Handler objects
     */
    private $handlers = [];

    /**
     * Handler factory
     */
    private $factory;

    /**
     * Add WordPress hooks for admin-post handlers
     */
    public function register_handlers() : void
    {
        foreach ( $this->get_handler_defs() as $params )
        {
            $handler = $this->factory()->create_handler( $params );
            $handler->add_hooks();
            $this->handlers[ $handler->get_action() ] = $handler;
        }
    }

    /**
     * Get handler definitions
     */
    private function get_handler_defs() : array
    {
        return apply_filters( 'mtgtools_admin_post_handler_definitions', $this->defs );
    }

    /**
     * Get handler factory
     */
    private function factory() : Post_Handler_Factory
    {
        if ( !isset( $this->factory ) )
        {
            $this->factory = new Post_Handler_Factory();
        }
        return $this->factory;
    }

}   // End of class