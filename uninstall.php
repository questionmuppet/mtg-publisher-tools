<?php
/**
 * Uninstall script for Mtg Publisher Tools plugin
 * 
 * @hooked Plugin uninstall
 */

// Exit if not initiated by uninstall trigger
defined( 'WP_UNINSTALL_PLUGIN' ) || die("Don't mess with it!");

// Define constants
require_once( plugin_dir_path( __FILE__ ) . 'constants.php' );

// Enable autoloading
require_once( MTGTOOLS__PATH . 'vendor/autoload.php' );

// Run uninstall procedure
$installation = new Mtgtools\Mtgtools_Installation();
$installation->uninstall();