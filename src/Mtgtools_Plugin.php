<?php
/**
 * Mtgtools_Plugin
 * 
 * Main plugin class
 */

namespace Mtgtools;

// Module dependencies
use Mtgtools\Symbols\Symbol_Db_Ops;
use Mtgtools\Interfaces\Mtg_Data_Source;
use Mtgtools\Scryfall\Scryfall_Data_Source;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab_Factory;

// Helper classes
use Mtgtools\Enqueue;
use Mtgtools\Notices\Admin_Notice;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Plugin
{
	/**
	 * Submodules
	 */
	private $symbols;
	private $dashboard;
	private $admin_posts;

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
		$this->dashboard()->add_hooks();
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
			$this->symbols = new Mtgtools_Symbols( $db_ops, $this->get_mtg_data_source(), $this );
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
			$this->dashboard = new Mtgtools_Dashboard( $factory, $this );
		}
		return $this->dashboard;
	}

	/**
	 * Get admin post module
	 */
	public function admin_posts() : Mtgtools_Admin_Posts
	{
		if ( !isset( $this->admin_posts ) )
		{
			$this->admin_posts = new Mtgtools_Admin_Posts( $this );
		}
		return $this->admin_posts;
	}

	/**
	 * -------------------------------
	 *   H E L P E R   M E T H O D S
	 * -------------------------------
	 */

	/**
	 * Get task library
	 */
	public function task_library() : Task_Library
	{
		if ( !isset( $this->task_library ) )
		{
			$this->task_library = new Task_Library();
		}
		return $this->task_library;
	}

	/**
	 * Enqueue a CSS asset
	 */
	public function add_style( array $args ) : void
	{
		$asset = new Enqueue\Css_Asset( $args );
		$asset->enqueue();
	}

	/**
	 * Enqueue a JS asset
	 */
	public function add_script( array $args ) : void
	{
		$asset = new Enqueue\Js_Asset( $args );
		$asset->enqueue();
	}

	/**
	 * Output a WordPress admin notice
	 */
	public function add_admin_notice( array $params ) : void
	{
		$notice = new Admin_Notice( $params );
		$notice->print();
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