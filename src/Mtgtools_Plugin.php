<?php
/**
 * Mtgtools_Plugin
 * 
 * Main plugin class
 */

namespace Mtgtools;
use Mtgtools\Symbols\Symbol_Db_Ops;
use Mtgtools\Interfaces\Mtg_Data_Source;
use Mtgtools\Scryfall\Scryfall_Data_Source;

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
		$this->dashboard()->add_hooks();
    }
    
	/**
	 * Get mana symbols module
	 */
    public function symbols() : Mtgtools_Symbols
    {
		if ( !isset( $this->symbols ) )
		{
			global $wpdb;
			$db_ops = new Symbol_Db_Ops( $wpdb );
			$this->symbols = new Mtgtools_Symbols( $db_ops, $this->enqueue(), $this->get_mtg_data_source() );
		}
		return $this->symbols;
	}

	/**
	 * Get dashboard module
	 */
	public function dashboard() : Mtgtools_Dashboard
	{
		if ( !isset( $this->dashboard ) )
		{
			$this->dashboard = new Mtgtools_Dashboard( $this->enqueue() );
		}
		return $this->dashboard;
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

	/**
	 * ---------------------------
	 *   D A T A   S O U R C E S
	 * ---------------------------
	 */

	/**
	 * Get Magic: The Gathering data source
	 */
	private function get_mtg_data_source() : Mtg_Data_Source
	{
		return apply_filters( 'mtgtools_mtg_data_source', $this->get_scryfall_source() );
	}

	/**
	 * Get Scryfall data source
	 */
	private function get_scryfall_source() : Scryfall_Data_Source
	{
		return new Scryfall_Data_Source();
	}

}   // End of class