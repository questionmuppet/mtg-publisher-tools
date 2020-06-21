<?php
/**
 * Scryfall_Object_Request
 * 
 * Performs a Scryfall API request, expecting object response
 */

namespace Mtgtools\Scryfall\Requests;
use Mtgtools\Exceptions\Scryfall\ScryfallException;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Scryfall_Object_Request extends Scryfall_Request
{
    /**
     * Get results from API call
     */
    public function get_data() : object
    {
        $object = $this->fetch();
        if ( !$this->is_expected_type( $object ) )
        {
            throw new ScryfallException( "A Scryfall API call returned an unexpected response type. Expected type: '{$this->get_type()}'; returned type: '{$object->object}'." );
        }
        return $object;
    }

}   // End of class