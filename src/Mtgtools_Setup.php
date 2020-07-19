<?php
/**
 * Mtgtools_Setup
 * 
 * Handles plugin installation and activation
 */

namespace Mtgtools;

use Mtgtools\Cards\Card_Db_Ops;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Setup
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
        $this->mtgtools()->cards_db()->create_tables();
        $this->mtgtools()->cron()->schedule_update_checks();
    }

    /**
     * Deactivate
     */
    public function deactivate() : void
    {
        $this->mtgtools()->cron()->cancel_update_checks();
    }

    /**
     * Uninstall
     */
    public function uninstall() : void
    {
        $this->mtgtools()->symbols()->delete_db_tables();
        $this->mtgtools()->cards_db()->drop_tables();
    }

    /**
     * Get plugin instance
     */
    private function mtgtools() : Mtgtools_Plugin
    {
        return $this->plugin;
    }

}   // End of class