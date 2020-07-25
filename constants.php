<?php
/**
 * Plugin constants
 */

// Exit if accessed directly
defined( 'ABSPATH' ) or die("Don't mess with it!");

// Define constants
define( 'MTGTOOLS__ADMIN_SLUG', 'mtgtools' );
define( 'MTGTOOLS__PATH', plugin_dir_path( __FILE__ ) );
define( 'MTGTOOLS__TEMPLATE_PATH', MTGTOOLS__PATH . 'templates/' );
define( 'MTGTOOLS__ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );