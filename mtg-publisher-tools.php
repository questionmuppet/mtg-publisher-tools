<?php
/*
Plugin Name: MTG Publisher Tools
Plugin URI: https://github.com/questionmuppet/mtg-publisher-tools
Description: Creates shortcodes for use in Magic: The Gathering articles and blog posts, including mana symbols and card images.
Version: 0.1.0
Author: Jason Schousboe
Author URI: https://github.com/questionmuppet
License: GNU General Public License v3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: mtg-publisher-tools
*/

// Exit if accessed directly
defined( 'ABSPATH' ) or die("Don't mess with it!");

// Paths
define( 'MTGTOOLS__ADMIN_SLUG', 'mtg-tools' );
define( 'MTGTOOLS__PATH', plugin_dir_path( __FILE__ ) );
define( 'MTGTOOLS__TEMPLATE_PATH', MTGTOOLS__PATH . 'templates/' );
define( 'MTGTOOLS__ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );

// Enable autoloading
require_once( MTGTOOLS__PATH . 'vendor/autoload.php' );

// Initialize plugin
add_action( 'init', array( Mtg_Publisher_Tools\Mtg_Tools_Plugin::get_instance(), 'init' ) );