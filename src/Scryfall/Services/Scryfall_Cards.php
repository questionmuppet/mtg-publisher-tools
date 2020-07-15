<?php
/**
 * Scryfall_Cards
 * 
 * Fetches card data from Scryfall API
 */

namespace Mtgtools\Scryfall\Services;

use Mtgtools\Cards;
use Mtgtools\Exceptions\Api as Exceptions;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Scryfall_Cards extends Scryfall_Api_Handler
{
    /**
     * Valid card search schemes
     */
    private $search_schemes = [
        'scryfall_id' => ['uuid'],
        'collector_number' => ['set_code', 'collector_number'],
        'name' => ['name'],
    ];

    /**
     * ---------------
     *   S E A R C H
     * ---------------
     */

    /**
     * Fetch a single card matching search filters
     * 
     * @param array $filter One or more filters conforming to a valid search scheme. Priority is granted to the most specific scheme.
     * @throws ApiException
     */
    public function fetch_card_by_filters( array $filters ) : Cards\Magic_Card
    {
        $method = $this->find_search_scheme( $filters );
        $data = call_user_func( $method, $filters );
        return $this->create_card( $data );
    }

    /**
     * Find highest priority search scheme from a filter set
     * 
     * @param array $filters    Search filters provided by client
     * @return callable         Method to execute to retrieve data using scheme
     */
    private function find_search_scheme( array $filters ) : callable
    {
        foreach ( $this->search_schemes as $key => $required )
        {
            if ( $this->contains_required( $filters, $required ) )
            {
                return array( $this, "fetch_card_by_{$key}" );
            }
        }
        throw new Exceptions\ScryfallParameterException(
            sprintf(
                "%s tried to search Scryfall for a Magic card using an invalid search scheme. No scheme is defined for the filter set: %s.",
                get_called_class(),
                implode( ',', array_keys( $filters ) )
            )
        );
    }

    /**
     * Check for required keys in an array
     */
    private function contains_required( array $params, array $required ) : bool
    {
        foreach ( $required as $key )
        {
            if ( !strlen( $params[ $key ] ?? '' ) )
            {
                return false;
            }
        }
        return true;
    }
    
    /**
     * -------------------------------
     *   S E A R C H   S C H E M E S
     * -------------------------------
     */

    /**
     * Fetch card by unique Scryfall id
     * 
     * @param string $filters['uuid']               (required)
     */
    private function fetch_card_by_scryfall_id( array $filters ) : array
    {
        $uuid = $filters['uuid'];
        return $this->get_endpoint([ 'endpoint' => "cards/{$uuid}" ]);
    }
    
    /**
     * Fetch card by set and collector number
     * 
     * @param string $filters['set_code']           (required)
     * @param string $filters['collector_number']   (required)
     * @param string $filters['language']           (optional, default English)
     */
    private function fetch_card_by_collector_number( array $filters ) : array
    {
        $identifiers = array_filter([
            $filters['set_code'],
            $filters['collector_number'],
            $filters['language'] ?? '',
        ]);
        return $this->get_endpoint([
            'endpoint' => "cards/" . implode( '/', $identifiers )
        ]);
    }

    /**
     * Fetch card by name
     * 
     * @param string $filters['name']               (required)
     * @param string $filters['set_code']           (optional, default newest edition)
     */
    private function fetch_card_by_name( array $filters ) : array
    {
        $name = $filters['name'];
        $set = $filters['set_code'] ?? '';

        $terms = array_filter([
            sprintf( '!"%s"', $name ),
            empty( $set ) ? '' : "e:{$set}",
        ]);
        $query = urlencode( implode( ' ', $terms ) );
        $list = $this->get_list_endpoint( "cards/search?q={$query}" );
        return $list[0];
    }

    /**
     * ---------------------------------
     *   O B J E C T   C R E A T I O N
     * ---------------------------------
     */

    /**
     * Create a Magic_Card object from response data
     */
    private function create_card( array $data ) : Cards\Magic_Card
    {
        return new Cards\Magic_Card([
            'uuid' => $data['id'],
            'name' => $data['name'],
            'set_code' => $data['set'],
            'language' => $data['lang'],
            'collector_number' => $data['collector_number'],
            'images' => $this->create_image_uris( $data ),
        ]);
    }

    /**
     * Create Image_Uri objects from response data
     * 
     * @return Image_Uri[]
     */
    private function create_image_uris( array $data ) : array
    {
        $images = [];
        foreach ( $this->find_image_uris_in_card_data( $data ) as $type => $uri )
        {
            $image = new Cards\Image_Uri([
                'card_uuid' => $data['id'],
                'uri' => $uri,
                'type' => $type,
            ]);
            $images[ $image->get_type() ] = $image;
        }
        return $images;
    }

    /**
     * Find image uris in response data
     * 
     * @return array Image uris from parent, dfc front-face, or empty array if not found
     */
    private function find_image_uris_in_card_data( array $data ) : array
    {
        return $data['image_uris']
            ?? $data['card_faces'][0]['image_uris']
            ?? [];
    }

}   // End of class