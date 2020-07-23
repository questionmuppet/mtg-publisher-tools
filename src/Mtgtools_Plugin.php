<?php
/**
 * Mtgtools_Plugin
 * 
 * Main plugin class
 */

namespace Mtgtools;

use Mtgtools\Db\Services\Symbol_Db_Ops;
use Mtgtools\Db\Services\Card_Db_Ops;
use Mtgtools\Interfaces\Mtg_Data_Source;
use Mtgtools\Scryfall\Scryfall_Data_Source;
use Mtgtools\Scryfall\Services;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab_Factory;
use Mtgtools\Wp_Tasks\Options;
use Mtgtools\Cards\Card_Cache;

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
	private $images;
	private $action_links;
	private $editor;
	private $cron;
	private $setup;

	/**
	 * Database services
	 */
	private $database;

	/**
	 * Plugin options
	 */
	private $options_manager;

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
		$this->images()->add_hooks();
		if ( is_admin() && is_user_logged_in() )
		{
			$this->dashboard()->add_hooks();
			$this->updates()->add_hooks();
			$this->settings()->add_hooks();
			$this->action_links()->add_hooks();
			$this->editor()->add_hooks();
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
			$db_ops = $this->database()->symbols();
			$source = $this->get_mtg_data_source();
			$this->symbols = new Mtgtools_Symbols( $db_ops, $source, $this );
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
	 * Get updates module
	 */
	public function updates() : Mtgtools_Updates
	{
		if ( !isset( $this->updates ) )
		{
			$db_ops = $this->database()->symbols();
			$source = $this->get_mtg_data_source();
			$this->updates = new Mtgtools_Updates( $db_ops, $source, $this );
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
			$options = $this->options_manager();
			$this->settings = new Mtgtools_Settings( $options, $this );
		}
		return $this->settings;
	}

	/**
	 * Get images module
	 */
	public function images() : Mtgtools_Images
	{
		if ( !isset( $this->images ) )
		{
			$source = $this->get_mtg_data_source();
			$cache = new Card_Cache( $this->database()->cards(), $source, $this );
			$this->images = new Mtgtools_Images( $cache, $source->get_default_image_type(), $this );
		}
		return $this->images;
	}

	/**
	 * Get action links module
	 */
	public function action_links() : Mtgtools_Action_Links
	{
		if ( !isset( $this->action_links ) )
		{
			$this->action_links = new Mtgtools_Action_Links( $this );
		}
		return $this->action_links;
	}

	/**
	 * Get editor module
	 */
	public function editor() : Mtgtools_Editor
	{
		if ( !isset( $this->editor ) )
		{
			$this->editor = new Mtgtools_Editor( $this );
		}
		return $this->editor;
	}

	/**
	 * Get cron module
	 */
	public function cron() : Mtgtools_Cron
	{
		if ( !isset( $this->cron ) )
		{
			$this->cron = new Mtgtools_Cron( $this->updates(), $this );
		}
		return $this->cron;
	}

	/**
	 * Get setup module
	 */
	public function setup() : Mtgtools_Setup
	{
		if ( !isset( $this->setup ) )
		{
			$this->setup = new Mtgtools_Setup( $this );
		}
		return $this->setup;
	}

	/**
	 * -------------------------------
	 *   P L U G I N   O P T I O N S
	 * -------------------------------
	 */

	/**
	 * Get options manager
	 */
	public function options_manager() : Options\Options_Manager
	{
		if ( !isset( $this->options_manager ) )
		{
			$factory = new Options\Option_Factory();
			$this->options_manager = new Options\Options_Manager( $factory );
			foreach ( $this->get_option_defs() as $key => $params )
			{
				$this->options_manager->register_option( $key, $params );
			}
		}
		return $this->options_manager;
	}

	/**
	 * Get plugin option definitions
	 */
	private function get_option_defs() : array
	{
		return [
			'inline_image_type' => [
				'type' => 'select',
				'label' => 'Inline image size',
				'default_value' => $this->get_mtg_data_source()->get_default_image_type(),
				'options_callback' => array( $this->get_mtg_data_source(), 'get_image_types' ),
			],
			'lazy_fetch_images' => [
				'type' => 'checkbox',
				'label' => 'Image uris',
				'default_value' => true,
				'input_args' => [
					'label' => 'Fetch card images lazily.',
				],
			],
			'image_cache_period_in_seconds' => [
				'type' => 'select',
				'label' => 'Refresh cached images',
				'default_value' => MONTH_IN_SECONDS,
				'options' => [
					DAY_IN_SECONDS => 'Daily',
					WEEK_IN_SECONDS => 'Weekly',
					MONTH_IN_SECONDS => 'Monthly',
					YEAR_IN_SECONDS => 'Yearly',
				],
			],
			'popup_tooltip_location' => [
				'type' => 'select',
				'label' => 'Image popup location (relative to link)',
				'default_value' => 'right',
				'options' => [
					'left' => 'Left',
					'right' => 'Right',
					'top' => 'Top',
					'bottom' => 'Bottom',
				],
			],
			'default_language' => [
				'type' => 'select',
				'label' => 'Default language for card images',
				'default_value' => $this->get_mtg_data_source()->get_default_language(),
				'options_callback' => array( $this->get_mtg_data_source(), 'get_languages' ),
			],
			'check_for_updates' => [
				'type' => 'checkbox',
				'default_value' => true,
				'label' => 'Update checker',
				'input_args' => [
					'label' => 'Check for updates automatically',
				],
			],
			'show_update_notices' => [
				'type' => 'checkbox',
				'default_value' => true,
				'label' => 'Admin notices',
				'input_args' => [
					'label' => 'Notify me about updates and connection issues on the WordPress dashboard',
				],
			],
		];
	}

	/**
	 * ---------------------------------
	 *   M O D U L E   S E R V I C E S
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
	 * Get database services
	 */
	public function database() : Database_Services
	{
		if ( !isset( $this->database ) )
		{
			global $wpdb;
			$this->database = new Database_Services( $wpdb );
		}
		return $this->database;
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
		$symbols = new Services\Scryfall_Symbols();
		$cards = new Services\Scryfall_Cards();
		return new Scryfall_Data_Source( $symbols, $cards );
	}

}   // End of class