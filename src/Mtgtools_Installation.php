<?php
/**
 * Mtgtools_Installation
 * 
 * Handles plugin installation and activation
 */

namespace Mtgtools;
use Mtgtools\Abstracts\Module;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Installation extends Module
{
    /**
     * Activate
     */
    public function activate()
    {
        $this->mtgtools()->symbols()->install_db_tables();
        $this->mtgtools()->symbols()->import_symbols();
    }

    /**
     * Uninstall
     */
    public function uninstall()
    {
        $this->mtgtools()->symbols()->delete_db_tables();
    }

}   // End of class