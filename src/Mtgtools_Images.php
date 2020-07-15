<?php
/**
 * Mtgtools_Images
 * 
 * Module for downloading, cacheing, and outputting card images
 */

namespace Mtgtools;

use Mtgtools\Abstracts\Module;
use Mtgtools\Cards\Card_Db_Ops;
use Mtgtools\Cards\Card_Link;
use Mtgtools\Interfaces\Mtg_Data_Source;

use Mtgtools\Exceptions\Db\DbException;
use Mtgtools\Exceptions\Cache\CacheException;
use Mtgtools\Exceptions\Cache\MissingDataException;
use Mtgtools\Exceptions\Api\ApiException;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Images extends Module
{
    /**
     * Search filters
     */
    private $search_filters = [
        'id',
        'name',
        'set',
        'number',
        'language'
    ];

    /**
     * Options
     */
    private $cache_period;

    /**
     * Dependencies
     */
    private $db_ops;
    private $source;

    /**
     * Constructor
     */
    public function __construct( Card_Db_Ops $db_ops, Mtg_Data_Source $source, $plugin )
    {
        $this->db_ops = $db_ops;
        $this->source = $source;
        parent::__construct( $plugin );
    }

    /**
     * Add WordPress hooks
     */
    public function add_hooks() : void
    {
        add_shortcode( 'mtg_card', array( $this, 'add_card_link' ) );
        $this->register_post_handlers([
            [
                'type' => 'ajax',
                'action' => 'mtgtools_find_image_uri',
                'callback' => array( $this, 'find_image_uri' ),
                'user_args' => $this->get_valid_search_filters(),
            ]
        ]);
    }

    /**
     * -------------------------------
     *   L I N K   S H O R T C O D E
     * -------------------------------
     */

    /**
     * Add card link
     * 
     * @param array $atts   Optional list of search criteria
     * @return string       Content wrapped in link
     */
    public function add_card_link( $atts, $content = '' ) : string
    {
        $link = new Card_Link(
            [
                'filters' => wp_parse_args( $atts ),
                'content' => $content,
                'is_ajax' => $this->fetching_lazily(),
            ],
            $this
        );
        return $link->get_markup();
    }

    /**
     * -----------------------
     *   I M A G E   U R I S
     * -----------------------
     */

    /**
     * Locate a card image uri
     * 
     * @param array $filters    One or more parameters matching a valid search scheme
     * @param string $type      Image type, defaults to admin setting
     * @return string           Uri to remote image file, empty string on failure
     */
    public function find_image_uri( array $filters, string $type = null ) : string
    {
        try
        {
            $filters = $this->validate_filters( $filters );
            return count( $filters )
                ? $this->locate_image_uri(
                    $filters,
                    $type ?? $this->get_popup_image_type()
                )
                : '';
        }
        catch ( ApiException $e )
        {
            return '';
        }
    }

    /**
     * Convert user-readable args to standardized filter keys
     */
    private function validate_filters( array $filters ) : array
    {
        return array_filter(
            [
                'name' => sanitize_text_field( $filters['name'] ?? '' ),
                'uuid' => sanitize_text_field( $filters['id'] ?? '' ),
                'set_code' => sanitize_text_field( $filters['set'] ?? '' ),
                'collector_number' => sanitize_text_field( $filters['number'] ?? '' ),
                'language' => sanitize_text_field( $filters['language'] ?? '' ),
            ],
            'strlen'
        );
    }

    /**
     * Get card image uri from db or data source
     * 
     * @throws ApiException
     */
    private function locate_image_uri( array $filters, string $type ) : string
    {
        try
        {
            return $this->get_cached_image_uri( $filters, $type );
        }
        catch ( CacheException $e )
        {
            $card = $this->source()->fetch_card( $filters );    // fetch remotely
            $this->db_ops()->cache_card_data( $card, $type );
            return $card->get_image_uri( $type );
        }
    }

    /**
     * Get image uri from cache in db
     * 
     * @param array $filters    Search parameters
     * @param string $type      Image type
     * @return string           Valid, non-expired image uri
     * @throws CacheException
     */
    private function get_cached_image_uri( array $filters, string $type ) : string
    {
        try
        {
            $card = $this->db_ops()->find_card( $filters );
        }
        catch( DbException $e )
        {
            throw new MissingDataException( "Requested data for a Magic card that's missing from the db. No record found for the filters provided." );
        }
        return $card->get_image_uri( $type );
    }

    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * Get db ops class
     */
    private function db_ops() : Card_Db_Ops
    {
        if ( !isset( $this->cache_period ) )
        {
            $this->cache_period = $this->get_cache_period();
            $this->db_ops->set_cache_period( $this->cache_period );
        }
        return $this->db_ops;
    }

    /**
     * Get MTG data source
     */
    private function source() : Mtg_Data_Source
    {
        return $this->source;
    }
    
    /**
     * -----------------
     *   O P T I O N S
     * -----------------
     */

    /**
     * Check for lazy fetch
     */
    private function fetching_lazily() : bool
    {
        return $this->get_plugin_option( 'lazy_fetch_images' );
    }

    /**
     * Get image type for popups
     */
    private function get_popup_image_type() : string
    {
        return $this->get_plugin_option( 'popup_image_type' );
    }

    /**
     * Get image cache period
     */
    private function get_cache_period() : int
    {
        return (int) $this->get_plugin_option( 'image_cache_period_in_seconds' );
    }

    /**
     * Get valid search filters
     */
    private function get_valid_search_filters() : array
    {
        return $this->search_filters;
    }

}   // End of class