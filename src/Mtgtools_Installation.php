<?php
/**
 * Mtgtools_Installation
 * 
 * Handles plugin installation and activation
 */

namespace Mtgtools;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Installation
{
    /**
     * Plugin instance
     */
    private $plugin;
    
    /**
     * Constructor
     */
    public function __construct( Mtgtools_Plugin $plugin )
    {
        $this->plugin = $plugin;
    }

    /**
     * Activate
     */
    public function activate() : void
    {
        $this->mtgtools()->symbols()->install_db_tables();
        $this->mtgtools()->symbols()->import_symbols();
    }

    /**
     * Deactivate
     */
    public function deactivate() : void
    {

    }

    /**
     * Uninstall
     */
    public function uninstall() : void
    {
        $this->mtgtools()->symbols()->delete_db_tables();
    }

    /**
     * Get plugin instance
     */
    private function mtgtools() : Mtgtools_Plugin
    {
        return $this->plugin;
    }

}   // End of class