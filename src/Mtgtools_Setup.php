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
        $this->plugin()->database()->install();
        $this->plugin()->symbols()->import_symbols();
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
        $this->plugin()->database()->uninstall();
        $this->plugin()->options_manager()->delete_options();
    }

}   // End of class