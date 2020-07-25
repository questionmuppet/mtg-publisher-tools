<?php
/**
 * Css_Asset
 * 
 * Enqueues a CSS file
 */

namespace Mtgtools\Wp_Tasks\Enqueue;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Css_Asset extends Asset
{
    /**
     * Path relative to assets url
     */
    protected $directory = 'css';
    
    /**
     * Enqueue asset in WP
     */
    public function enqueue() : void
    {
        wp_enqueue_style(
            $this->get_handle(),
            $this->get_path(),
            $this->get_deps(),
            $this->get_version()
        );
    }

}   // End of class