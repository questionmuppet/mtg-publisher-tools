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
use Mtgtools\Dashboard\Tabs\Dashboard_Tab_Factory;
use Mtgtools\Wp_Tasks\Options\Option_Factory;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Plugin
{
	/**
	 * Submodules
	 */
	private $symbols;
	private $dashboard;
	private $updates;
	private $settings;

	/**
	 * Module task library
	 */
	private $task_library;

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
		if ( is_admin() && is_user_logged_in() )
		{
			$this->dashboard()->add_hooks();
			$this->updates()->add_hooks();
			$this->settings()->add_hooks();
		}
	}
	
	/**
	 * -----------------
	 *   M O D U L E S
	 * -----------------
	 */
    
	/**
	 * Get mana symbols module
	 */
    public function symbols() : Mtgtools_Symbols
    {
		if ( !isset( $this->symbols ) )
		{
			global $wpdb;
			$db_ops = new Symbol_Db_Ops( $wpdb );
			$this->symbols = new Mtgtools_Symbols( $db_ops, $this->get_mtg_data_source(), $this->wp_tasks() );
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
			$factory = new Dashboard_Tab_Factory();
			$this->dashboard = new Mtgtools_Dashboard( $factory, $this->wp_tasks() );
		}
		return $this->dashboard;
	}

	/**
	 * Get updates module
	 */
	public function updates() : Mtgtools_Updates
	{
		if ( !isset( $this->updates ) )
		{
			global $wpdb;
			$db_ops = new Symbol_Db_Ops( $wpdb );
			$this->updates = new Mtgtools_Updates( $db_ops, $this->get_mtg_data_source(), $this->wp_tasks() );
		}
		return $this->updates;
	}

	/**
	 * Get settings module
	 */
	public function settings() : Mtgtools_Settings
	{
		if ( !isset( $this->settings ) )
		{
			$factory = new Option_Factory();
			$this->settings = new Mtgtools_Settings( $factory, $this->wp_tasks() );
		}
		return $this->settings;
	}

	/**
	 * ---------------------------------
	 *   W P   T A S K   L I B R A R Y
	 * ---------------------------------
	 */

	/**
	 * Get Wp Task library
	 */
	public function wp_tasks() : Wp_Task_Library
	{
		if ( !isset( $this->wp_tasks ) )
		{
			$this->wp_tasks = new Wp_Task_Library();
		}
		return $this->wp_tasks;
	}

	/**
	 * ---------------------------
	 *   D A T A   S O U R C E S
	 * ---------------------------
	 */

	/**
	 * Get Magic: The Gathering data source
	 */
	public function get_mtg_data_source() : Mtg_Data_Source
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

	/**
	 * ---------------------------
	 *   I N S T A L L A T I O N
	 * ---------------------------
	 */

	/**
	 * Activate plugin
	 * 
	 * @hooked activate_mtg-publisher-tools/mtg-publisher-tools.php
	 */
	public function activate() : void
	{
		$this->setup()->activate();
	}

	/**
	 * Deactivate plugin
	 * 
	 * @hooked deactivate_mtg-publisher-tools/mtg-publisher-tools.php
	 */
	public function deactivate() : void
	{
		$this->setup()->deactivate();
	}

	/**
	 * Uninstall plugin
	 */
	public function uninstall() : void
	{
		$this->setup()->uninstall();
	}

	/**
	 * Get setup module
	 */
	private function setup() : Mtgtools_Setup
	{
		return new Mtgtools_Setup( $this );
	}

}   // End of class