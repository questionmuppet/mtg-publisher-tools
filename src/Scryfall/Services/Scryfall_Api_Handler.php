<?php
/**
 * Scryfall_Api_Handler
 * 
 * Abstract class for making high-level calls to the Scryfall API
 */

namespace Mtgtools\Scryfall\Services;
use Mtgtools\Scryfall\Requests;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Scryfall_Api_Handler
{
    /**
     * Get endpoint
     */
    protected function get_endpoint( array $args ) : array
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

    /**
     * Get list endpoint
     */
    protected function get_list_endpoint( string $endpoint ) : array
    {
        $paginator = new Requests\Scryfall_List_Paginator([
            'endpoint' => $endpoint
        ]);
        return $paginator->get_full_list();
    }

}   // End of class