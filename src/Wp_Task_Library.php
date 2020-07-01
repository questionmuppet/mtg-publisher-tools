<?php
/**
 * Wp_Task_Library
 * 
 * Produces task objects to interface with a part of the WordPress api.
 */

namespace Mtgtools;

use Mtgtools\Wp_Tasks\Enqueue;
use Mtgtools\Wp_Tasks\Templates\Template;
use Mtgtools\Wp_Tasks\Tables\Table_Data;
use Mtgtools\Wp_Tasks\Notices\Admin_Notice;
use Mtgtools\Wp_Tasks\Admin_Post\Post_Handler_Factory;
use Mtgtools\Wp_Tasks\Admin_Post\Admin_Post_Handler;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Wp_Task_Library
{
    /**
     * Factory instances
     */
    private $factories = [];

    /**
     * Create a CSS asset
     */
    public function create_style( array $params ) : Enqueue\Css_Asset
    {
        return new Enqueue\Css_Asset( $params );
    }
    
    /**
     * Create a JavaScript asset
     */
    public function create_script( array $params ) : Enqueue\Js_Asset
    {
        return new Enqueue\Js_Asset( $params );
    }

    /**
     * Create a template for outputting HTML
     */
    public function create_template( array $params ) : Template
    {
        return new Template( $params );
    }

    /**
     * Create table for display on dashboard
     */
    public function create_table( array $params ) : Table_Data
    {
        return new Table_Data( $params );
    }

    /**
     * Create an admin notice
     */
    public function create_admin_notice( array $params ) : Admin_Notice
    {
        return new Admin_Notice( $params );
    }

    /**
     * Create an admin-post handler
     */
    public function create_post_handler( array $params ) : Admin_Post_Handler
    {
        return $this->post_handler_factory()->create_handler( $params );
    }

    /**
     * Get post handler factory
     */
    private function post_handler_factory() : Post_Handler_Factory
    {
        if ( !isset( $this->factories['post_handler'] ) )
        {
            $this->factories['post_handler'] = new Post_Handler_Factory();
        }
        return $this->factories['post_handler'];
    }

}   // End of class