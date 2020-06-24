<?php
/**
 * Js_Asset
 * 
 * Enqueues a JavaScript file
 */

namespace Mtgtools\Enqueue;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Js_Asset extends Asset
{
    /**
     * Default properties
     */
    protected $defaults = array(
        'data' => [],
    );

    /**
     * Path relative to assets url
     */
    protected $directory = 'js';
    
    /**
     * Enqueue asset in WP
     */
    public function enqueue() : void
    {
        wp_enqueue_script(
            $this->get_handle(),
            $this->get_path(),
            $this->get_deps(),
            $this->get_version(),
            true // In footer
        );
        foreach ( $this->get_script_data() as $key => $data )
        {
            wp_localize_script( $this->get_handle(), $key, $data );
        }
    }

    /**
     * Get data to pass to script as JS objects
     */
    private function get_script_data() : array
    {
        return $this->get_prop( 'data' );
    }

}   // End of class