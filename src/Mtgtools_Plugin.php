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
	/**
	 * Submodules
	 */
	private $symbols;
	private $enqueue;

	/**
	 * Plugin instance
	 */
	private static $instance;
	
    /**
	 * Access plugin’s working instance
	 */
	public static function get_instance() : Mtgtools_Plugin
	{
		NULL === self::$instance and self::$instance = new self;
		return self::$instance;
    }
    
    /**
	 * Initialize plugin
	 *
	 * @hooked init
	 */
	public function init() : void
	{
		$this->symbols()->add_hooks();
    }
    
	/**
	 * Get mana symbols module
	 */
    public function symbols() : Mtgtools_Symbols
    {
		if ( !isset( $this->symbols ) )
		{
			global $wpdb;
			$db_ops = new \Mtgtools\Symbols\Symbol_Db_Ops( $wpdb );
			$this->symbols = new Mtgtools_Symbols( $db_ops );
		}
		return $this->symbols;
	}
	
	/**
	 * Get enqueue module
	 */
	public function enqueue() : Mtgtools_Enqueue
	{
		if ( !isset( $this->enqueue ) )
		{
			$this->enqueue = new Mtgtools_Enqueue();
		}
		return $this->enqueue;
	}

}   // End of class