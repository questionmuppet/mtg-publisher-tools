<?php
/**
 * Mtg_Data_Source
 * 
 * Interface that exposes external Magic: The Gathering data to consumer classes
 */

namespace Mtgtools\Interfaces;
use Mtgtools\Cards\Magic_Card;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

interface Mtg_Data_Source
{
    /**
     * Get all mana symbols
     * 
     * @return Mana_Symbol[]
     */
    public function get_mana_symbols() : array;

    /**
     * Fetch a card matching search filters
     */
    public function fetch_card( array $filters ) : Magic_Card;

    /**
     * Get available image types
     * 
     * @return array Associative array of "key" => "description"
     */
    public function get_image_types() : array;

    /**
     * Get name of source for display
     */
    public function get_display_name() : string;

    /**
     * Get uri to source documentation
     */
    public function get_documentation_uri() : string;

}   // End of interface