<?php
/*
Plugin Name: MTG Publisher Tools
Plugin URI: https://github.com/questionmuppet/mtg-publisher-tools
Description: Enables insertion of Magic: The Gathering content directly into your posts or theme. Add mana symbols and card images the easy way.
Version: 0.1.0
Requires at least: 5.4
Requires PHP: 7.3
Author: Jason Schousboe
Author URI: https://github.com/questionmuppet
License: GNU General Public License v3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: mtg-publisher-tools
*/

// Exit if accessed directly
defined( 'ABSPATH' ) or die("Don't mess with it!");

// Define constants
define( 'MTGTOOLS__VERSION', get_file_data( __FILE__, array( 'Version' => 'Version' ) )['Version'] );
require_once( plugin_dir_path( __FILE__ ) . 'constants.php' );

// Enable autoloading
require_once( MTGTOOLS__PATH . 'vendor/autoload.php' );

// Plugin instance
$plugin = Mtgtools\Mtgtools_Plugin::get_instance();

// Activation hooks
register_activation_hook( __FILE__, array( $plugin, 'activate' ) );
register_deactivation_hook( __FILE__, array( $plugin, 'deactivate' ) );

// Initialize
add_action( 'init', array( $plugin, 'init' ) );