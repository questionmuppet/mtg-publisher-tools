<?php
/**
 * Task_Library
 * 
 * Produces task objects for consumption by submodules
 */

namespace Mtgtools;

use Mtgtools\Tasks\Enqueue;
use Mtgtools\Tasks\Templates\Template;
use Mtgtools\Tasks\Tables\Table_Data;
use Mtgtools\Tasks\Notices\Admin_Notice;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Task_Library
{
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
     * Create a template
     */
    public function create_template( array $params ) : Template
    {
        return new Template( $params );
    }

    /**
     * Create table data
     */
    public function create_table_data( array $params ) : Table_Data
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

}   // End of class