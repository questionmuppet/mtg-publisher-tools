<?php
/**
 * Mtgtools_Enqueue
 * 
 * Enqueues JavaScript and CSS files
 */

namespace Mtgtools;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Enqueue
{
    /**
     * Plugin version for cache breaking
     */
    private $version;

    /**
     * Enqueue a CSS asset
     */
    public function add_style( array $args ) : void
    {
        $args = wp_parse_args( $args, [
            'key'  => '',
            'path' => '',
            'deps' => [],
        ]);
        wp_enqueue_style( $args['key'], $this->get_css_path( $args['path'] ), $args['deps'], $this->get_version() );
    }

    /**
     * Enqueue a JS asset
     */
    public function add_script( array $args ) : void
    {
        $args = wp_parse_args( $args, [
            'key'      => '',
            'path'     => '',
            'deps'     => [],
            'data_key' => 'mtgtoolsData',
            'data'     => null,
        ]);
        wp_enqueue_script( $args['key'], $this->get_js_path( $args['path'] ), $args['deps'], $this->get_version(), true );    // In footer
        if ( !is_null( $args['data'] ) )
        {
            wp_localize_script( $args['key'], $args['data_key'], $args['data'] );
        }
    }

    /**
     * Get full CSS path
     */
    private function get_css_path( string $coda ) : string
    {
        return MTGTOOLS__ASSETS_URL . 'css/' . $coda;
    }

    /**
     * Get full JS path
     */
    private function get_js_path( string $coda ) : string
    {
        return MTGTOOLS__ASSETS_URL . 'js/' . $coda;
    }

    /**
     * Get plugin version
     */
    protected function get_version() : string
    {
        if ( !isset( $this->version ) )
        {
            $this->version = get_file_data( MTGTOOLS__FILE, array( 'Version' => 'Version' ) )['Version'];
        }
        return $this->version;
    }

}   // End of class