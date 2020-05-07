<?php
/**
 * Mtgtools_Installation
 * 
 * Handles plugin installation and activation
 */

namespace Mtgtools;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

final class Mtgtools_Installation
{
    /**
     * Plugin instance
     */
    private $plugin;

    /**
     * Activate
     */
    public function activate()
    {
    }

    /**
     * Uninstall
     */
    public function uninstall()
    {
    }

    /**
     * Get plugin instance
     */
    private function mtgtools() : Mtgtools_Plugin
    {
        if ( !isset( $this->plugin ) )
        {
            $this->plugin = Mtgtools_Plugin::get_instance();
        }
        return $this->plugin;
    }

}   // End of class