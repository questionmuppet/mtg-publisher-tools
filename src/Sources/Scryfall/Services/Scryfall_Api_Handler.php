<?php
/**
 * Scryfall_Api_Handler
 * 
 * Abstract class for making high-level calls to the Scryfall API
 */

namespace Mtgtools\Sources\Scryfall\Services;
use Mtgtools\Sources\Scryfall\Requests;
use Mtgtools\Exceptions\Api\ApiException;
use Mtgtools\Exceptions\Sources\Scryfall as Exceptions;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Scryfall_Api_Handler
{
    /**
     * Get endpoint
     */
    protected function get_endpoint( array $args ) : array
    {
        try
        {
            $args = array_merge([
                'endpoint' => '',
                'expects'  => 'card',
                'method'   => 'GET',
                'body'     => null,
            ], $args );
    
            $request = new Requests\Scryfall_Request( $args );
            return $request->get_data();
        }
        catch ( ApiException $e )
        {
            $msg = sprintf(
                "Scryfall encountered an error during an API request. No results found for endpoint '%s'.",
                $args['endpoint']
            );
            throw new Exceptions\ScryfallApiException( $msg, 0, $e );
        }
    }

    /**
     * Get list endpoint
     */
    protected function get_list_endpoint( string $endpoint ) : array
    {
        try
        {
            $paginator = new Requests\Scryfall_List_Paginator([
                'endpoint' => $endpoint
            ]);
            return $paginator->get_full_list();
        }
        catch ( ApiException $e )
        {
            $msg = sprintf(
                "Scryfall encountered an error during an API request. No results found for endpoint '%s'.",
                $endpoint
            );
            throw new Exceptions\ScryfallApiException( $msg, 0, $e );
        }
    }

}   // End of class