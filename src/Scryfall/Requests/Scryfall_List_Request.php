<?php
/**
 * Scryfall_List_Request
 * 
 * Performs a Scryfall API call, expecting list response
 */

namespace Mtgtools\Scryfall\Requests;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Scryfall_List_Request extends Scryfall_Request
{
    /**
     * Get results from API call
     */
    public function get_data() : array
    {
        return [];
    }

}   // End of class