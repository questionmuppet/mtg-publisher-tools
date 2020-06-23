<?php
/*
Plugin Name: MTG Publisher Tools
Plugin URI: https://github.com/questionmuppet/mtg-publisher-tools
Description: Enables insertion of Magic: The Gathering content directly into your article or theme. Add mana symbols and card images the easy way.
Version: 0.1.0
Author: Jason Schousboe
Author URI: https://github.com/questionmuppet
License: GNU General Public License v3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: mtg-publisher-tools
*/

// Exit if accessed directly
defined( 'ABSPATH' ) or die("Don't mess with it!");

// Define constants
define( 'MTGTOOLS__FILE', __FILE__ );
require_once( plugin_dir_path( __FILE__ ) . 'constants.php' );

// Enable autoloading
require_once( MTGTOOLS__PATH . 'vendor/autoload.php' );

// Load namespaced functions
require_once( MTGTOOLS__PATH . 'functions.php' );

// Activation hooks
register_activation_hook( __FILE__, function() {
    $installation = new Mtgtools\Mtgtools_Installation();
    $installation->activate();
});

// Initialize plugin
add_action( 'init', array( Mtgtools\Mtgtools_Plugin::get_instance(), 'init' ) );