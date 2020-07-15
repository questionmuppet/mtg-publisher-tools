<?php
/**
 * Scryfall_Data_Source
 * 
 * Exposes Scryfall Api data using a standardized interface
 */

namespace Mtgtools\Scryfall;

use Mtgtools\Interfaces\Mtg_Data_Source;
use Mtgtools\Scryfall\Services\Scryfall_Symbols;
use Mtgtools\Scryfall\Services\Scryfall_Cards;
use Mtgtools\Cards\Magic_Card;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Scryfall_Data_Source implements Mtg_Data_Source
{
    /**
     * Api services
     */
    private $symbols;
    private $cards;

    /**
     * Constructor
     */
    public function __construct( Scryfall_Symbols $symbols, Scryfall_Cards $cards )
    {
        $this->symbols = $symbols;
        $this->cards = $cards;
    }

    /**
     * ---------------------------
     *   M A N A   S Y M B O L S
     * ---------------------------
     */

    /**
     * Get all mana symbols
     * 
     * @return Mana_Symbol[]
     */
    public function get_mana_symbols() : array
    {
        return $this->symbols()->get_all_symbols();
    }

    /**
     * -------------------------
     *   M A G I C   C A R D S
     * -------------------------
     */

    /**
     * Fetch a single card matching search filters
     * 
     * @throws ApiException
     */
    public function fetch_card( array $filters ) : Magic_Card
    {
        return $this->cards()->fetch_card_by_filters( $filters );
    }

    /**
     * -----------
     *   I N F O
     * -----------
     */

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

    /**
     * ---------------------------
     *   A P I   S E R V I C E S
     * ---------------------------
     */

    /**
     * Get symbols service
     */
    private function symbols() : Scryfall_Symbols
    {
        return $this->symbols;
    }

    /**
     * Get cards service
     */
    private function cards() : Scryfall_Cards
    {
        return $this->cards;
    }

}   // End of class