<?php
/**
 * Scryfall_Symbols
 * 
 * Fetches mana symbol data from Scryfall API
 */

namespace Mtgtools\Scryfall\Services;
use Mtgtools\Symbols\Mana_Symbol;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Scryfall_Symbols extends Scryfall_Api_Handler
{
    /**
     * Get all mana symbols
     * 
     * @return Mana_Symbol[]
     */
    public function get_all_symbols() : array
    {
        $symbols = [];
        foreach ( $this->get_list_endpoint('symbology') as $data )
        {
            $symbols[] = new Mana_Symbol([
                'plaintext'      => $data['symbol'],
                'english_phrase' => $data['english'],
                'svg_uri'        => $data['svg_uri'],
            ]);
        }
        return $symbols;
    }

}   // End of class