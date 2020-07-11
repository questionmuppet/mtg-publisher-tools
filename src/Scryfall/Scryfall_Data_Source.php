<?php
/**
 * Scryfall_Data_Source
 * 
 * Fetches MTG data from Scryfall API
 */

namespace Mtgtools\Scryfall;
use Mtgtools\Scryfall\Abstracts\Scryfall_Api_Handler;
use Mtgtools\Interfaces\Mtg_Data_Source;
use Mtgtools\Symbols\Mana_Symbol;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Scryfall_Data_Source extends Scryfall_Api_Handler implements Mtg_Data_Source
{
    /**
     * Get all mana symbols
     * 
     * @return Mana_Symbol[]
     */
    public function get_mana_symbols() : array
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

    /**
     * Get available image types
     */
    public function get_image_types() : array
    {
        return [
            'png' => 'High-resolution',
            'large' => 'Large',
            'normal' => 'Normal',
            'small' => 'Small',
            'border_crop' => 'Border crop',
            'art_crop' => 'Art crop',
        ];
    }

    /**
     * Get name for display
     */
    public function get_display_name() : string
    {
        return 'Scryfall API';
    }

    /**
     * Get documentation uri
     */
    public function get_documentation_uri() : string
    {
        return 'https://scryfall.com/docs/api/';
    }

}   // End of class