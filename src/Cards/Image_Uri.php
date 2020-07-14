<?php
/**
 * Image_Uri
 * 
 * Represents a cached image uri for a Magic card
 */

namespace Mtgtools\Cards;
use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Image_Uri extends Data
{
    /**
     * Required properties
     */
    protected $required = array(
        'card_uuid',
        'uri',
        'type',
    );
    
    /**
     * Default properties
     */
    protected $defaults = array(
        'cache_period' => WEEK_IN_SECONDS,
        'cached' => null,
    );

    /**
     * -----------------------
     *   E X P I R A T I O N
     * -----------------------
     */

    /**
     * Check for cache expiration
     */
    public function is_expired() : bool
    {
        return $this->was_cached() && time() > $this->get_expiration_timestamp();
    }
    
    /**
     * Get expiration as unix timestamp
     */
    private function get_expiration_timestamp() : int
    {
        return $this->get_cache_timestamp() + $this->get_cache_period();
    }
    
    /**
     * -------------
     *   P R O P S
     * -------------
     */

    /**
     * Get corresponding card uuid
     */
    public function get_card_uuid() : string
    {
        return $this->get_prop( 'card_uuid' );
    }

    /**
     * Get uri to image file
     */
    public function get_uri() : string
    {
        return $this->get_prop( 'uri' );
    }

    /**
     * Get image type
     */
    public function get_type() : string
    {
        return $this->get_prop( 'type' );
    }
    
    /**
     * Check for a unix timestamp
     */
    private function was_cached() : bool
    {
        return !is_null( $this->get_prop( 'cached' ) );
    }

    /**
     * Get unix timestamp of last cacheing
     */
    private function get_cache_timestamp() : int
    {
        return strtotime( $this->get_prop( 'cached' ) );
    }
    
    /**
     * Get admin setting for cache period
     * 
     * @return int Cache period expressed in seconds
     */
    private function get_cache_period() : int
    {
        return absint( $this->get_prop( 'cache_period' ) );
    }

}   // End of class