<?php
/**
 * Card_Cache
 * 
 * Downloads, caches, and retrieves Magic card data
 */

namespace Mtgtools\Cards;

use Mtgtools\Abstracts\Module;
use Mtgtools\Sources\Mtg_Data_Source;

use Mtgtools\Db\Services\Card_Db_Ops;

use Mtgtools\Exceptions\Cache;
use Mtgtools\Exceptions\Mtg;
use Mtgtools\Exceptions\Db\NoResultsException;
use Mtgtools\Exceptions\Sources\MtgSourceException;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Card_Cache extends Module
{
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
        parent::__construct( $plugin );
        $this->db_ops = $db_ops;
        $this->source = $source;
        $this->db_ops->set_cache_period( $this->get_cache_period() );
    }

    /**
     * Retrieve a Magic card from the cache or data source
     * 
     * @param array $filters    One or more parameters matching a valid search scheme
     * @param string $img_type  Image type to ensure present (when missing will trigger an API call)
     * @throws MtgDataException
     */
    public function locate_card( array $filters, string $img_type = null ) : Magic_Card
    {
        try
        {
            $filters = $this->validate_filters( $filters );
            return $this->find_cached_card( $filters, $img_type );
        }
        catch ( Cache\CacheException $e )
        {
            $card = $this->find_remote_card( $filters );
            $this->db_ops()->cache_card_data( $card, $img_type );
            return $card;
        }
    }

    /**
     * -------------------------------
     *   S E A R C H   S C H E M E S
     * -------------------------------
     */

    /**
     * Convert user args to a standardized search scheme
     * 
     * @throws MtgParameterException
     */
    private function validate_filters( array $filters ) : array
    {
        $filters = $this->find_search_scheme( array_filter( $filters, 'strlen' ) );
        if ( !count( $filters ) )
        {
            throw new Mtg\MtgParameterException( "A request for Magic card data was made with invalid search criteria. Your filters must match a valid search scheme." );
        }
        return $filters;
    }

    /**
     * Find highest priority search scheme in filters
     */
    private function find_search_scheme( array $args ) : array
    {
        if ( isset( $args['id'] ) )
        {
            // Search by unique id
            $filters = [
                'uuid' => sanitize_text_field( $args['id'] ),
                'backface' => isset( $args['backface'] ) ? boolval( $args['backface'] ) : null,
            ];
        }
        elseif ( isset( $args['set'], $args['number'] ) )
        {
            // Search by set + collector #
            $filters = [
                'set_code'         => strtolower( sanitize_text_field( $args['set'] ) ),
                'collector_number' => sanitize_text_field( $args['number'] ),
                'language'         => strtolower( sanitize_text_field( $args['language'] ?? $this->get_default_language() ) ),
            ];
        }
        else
        {
            // Search by name
            $filters = [
                'name' => sanitize_text_field( $args['name'] ?? '' ),
                'set_code' => sanitize_text_field( $args['set'] ?? '' ),
            ];
        }
        return array_filter( $filters, 'strlen' );
    }
    
    /**
     * -----------------------
     *   F R O M   C A C H E
     * -----------------------
     */

    /**
     * Find a Magic card in the db cache
     * 
     * @throws CacheException
     */
    private function find_cached_card( array $filters, string $type = null ) : Magic_Card
    {
        try
        {
            $card = $this->db_ops()->find_card( $filters );
            if ( !empty( $type ) )
            {
                $this->validate_cached_image( $card, $type );
            }
            return $card;
        }
        catch( NoResultsException $e )
        {
            throw new Cache\MissingDataException( "Requested a missing card from the db cache. No card matching the provided filters has been cached.", 0, $e );
        }
    }

    /**
     * Validate a cached image uri in a Magic card
     * 
     * @throws CacheException
     */
    private function validate_cached_image( Magic_Card $card, string $type ) : void
    {
        if ( !$card->has_image( $type ) )
        {
            throw new Cache\MissingDataException( "Requested a missing image uri from the db cache. No image of type '{$type}' was found for the specified Magic card." );
        }
        if ( $card->get_image( $type )->is_expired() )
        {
            throw new Cache\ExpiredDataException( "Requested an expired image uri from the db cache. Image must be refreshed from the external data source." );
        }
    }

    /**
     * -------------------------
     *   F R O M   R E M O T E
     * -------------------------
     */

    /**
     * Fetch a Magic card from the remote data source
     */
    private function find_remote_card( array $filters ) : Magic_Card
    {
        try
        {
            return $this->source()->fetch_card( $filters );
        }
        catch ( MtgSourceException $e )
        {
            throw new Mtg\MtgFetchException( get_called_class() . " failed to retrieve Magic card data from a remote source. No card was found matching the specified filters.", 0, $e );
        }
    }

    /**
     * -----------------
     *   O P T I O N S
     * -----------------
     */

    /**
     * Get image cache period
     */
    private function get_cache_period() : int
    {
        return (int) $this->get_plugin_option( 'image_cache_period_in_seconds' );
    }

    /**
     * Get default language
     */
    private function get_default_language() : string
    {
        return $this->get_plugin_option( 'default_language' );
    }

    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * Get db ops
     */
    private function db_ops() : Card_Db_Ops
    {
        return $this->db_ops;
    }

    /**
     * Get source
     */
    private function source() : Mtg_Data_Source
    {
        return $this->source;
    }

}   // End of class