<?php
/**
 * Global functions available in the plugin namespace
 */

namespace Mtgtools;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

/**
 * Load template file, allowing for themes to override
 * 
 * @param string $path      File path relative to 'templates' folder
 * @param array $params     Optional parameters to pass to the template
 */
function load_mtgtools_template( string $path, array $params = [] ) : void
{
    // Set query vars
    foreach ( $params as $key => $value )
    {
        set_query_var( $key, $value );
    }

    // Load template with highest priority
    $template = locate_template( $path );
    $template = strlen( $template )
        ? $template
        : MTGTOOLS__TEMPLATE_PATH . $path;
    load_template( $template, false );

    // Unset query vars
    foreach ( $params as $key => $value )
    {
        set_query_var( $key, null );
    }
}