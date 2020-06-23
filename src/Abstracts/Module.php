<?php
/**
 * Module
 * 
 * Abstract class for plugin submodules
 */

namespace Mtgtools\Abstracts;
use Mtgtools\Mtgtools_Plugin;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Module
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
     * Get plugin instance
     */
    protected function mtgtools() : Mtgtools_Plugin
    {
        return $this->plugin;
    }

}   // End of class