<?php
/**
 * Scryfall_Data_Source
 * 
 * Fetches MTG data from Scryfall API
 */

namespace Mtgtools\Scryfall;
use Mtgtools\Interfaces\Mtg_Data_Source;
use Mtgtools\Scryfall\Scryfall_Request;
use Mtgtools\Symbols\Mana_Symbol;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Scryfall_Data_Source implements Mtg_Data_Source
{
    /**
     * Get all mana symbols
     * 
     * @return Mana_Symbol[]
     */
    public function get_mana_symbols() : array
    {
        $symbols = [];
        $request = new Scryfall_Request([
            'endpoint' => 'symbology',
        ]);
        foreach ( $request->get_data() as $data )
        {
            $symbols[] = new Mana_Symbol([
                'plaintext'      => $data->symbol,
                'english_phrase' => $data->english,
                'svg_uri'        => $data->svg_uri,
            ]);
        }
        return $symbols;
    }

    /**
     * Request Factory
     * 
     * three types: simple, catalog, list
     */

}   // End of class