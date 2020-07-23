<?php
/**
 * Mtgtools_Setup
 * 
 * Handles plugin installation and activation
 */

namespace Mtgtools;
use Mtgtools\Abstracts\Module;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Setup extends Module
{
    /**
     * Activate
     */
    public function activate() : void
    {
        $this->plugin()->symbols()->install_db_tables();
        $this->plugin()->symbols()->import_symbols();
        $this->plugin()->cards_db()->create_tables();
        $this->plugin()->cron()->schedule_update_checks();
    }

    /**
     * Deactivate
     */
    public function deactivate() : void
    {
        $this->plugin()->cron()->cancel_update_checks();
    }

    /**
     * Uninstall
     */
    public function uninstall() : void
    {
        $this->plugin()->options_manager()->delete_options();
        $this->plugin()->symbols()->delete_db_tables();
        $this->plugin()->cards_db()->drop_tables();
    }

}   // End of class