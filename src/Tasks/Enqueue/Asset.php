<?php
/**
 * Asset
 * 
 * Abstract class for enqueueing JavaScript and CSS
 */

namespace Mtgtools\Tasks\Enqueue;
use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Asset extends Data
{
    /**
     * Required properties
     */
    protected $required = array(
        'key',
        'path',
    );

    /**
     * Default properties
     */
    protected $abstract_defaults = array(
        'deps'    => [],
        'version' => null
    );

    /**
     * Path relative to assets url
     */
    protected $directory = '';

    /**
     * Enqueue asset in WP
     */
    abstract public function enqueue(): void;

    /**
     * Get unique identifier
     */
    protected function get_handle() : string
    {
        return $this->get_prop( 'key' );
    }

    /**
     * Get full path to asset
     */
    protected function get_path() : string
    {
        return trailingslashit( MTGTOOLS__ASSETS_URL . $this->directory ) . $this->get_prop( 'path' );
    }

    /**
     * Get dependencies
     */
    protected function get_deps() : array
    {
        return $this->get_prop( 'deps' );
    }

    /**
     * Get version (for cache breaking)
     */
    protected function get_version() : string
    {
        return $this->get_prop( 'version' ) ?? MTGTOOLS__VERSION;
    }

}   // End of class