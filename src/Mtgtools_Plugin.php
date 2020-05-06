<?php
/**
 * Mtgtools_Plugin
 * 
 * Main plugin class
 */

namespace Mtgtools;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

final class Mtgtools_Plugin
{
	// Plugin instance
	private static $instance;
	
    /**
	 * Access plugin’s working instance
	 */
	public static function get_instance()
	{
		NULL === self::$instance and self::$instance = new self;
		return self::$instance;
    }
    
    /**
	 * Initialize plugin
	 *
	 * @hooked init
	 */
	public function init()
	{
		$this->add_hooks();
    }
    
	/**
	 * Add WordPress hooks
	 */
    protected function add_hooks()
    {
        
    }

}   // End of class