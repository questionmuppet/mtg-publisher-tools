<?php
/**
 * Mtg_Data_Source
 * 
 * Interface that exposes external Magic: The Gathering data to consumers
 */

namespace Mtgtools\Interfaces;

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

}   // End of interface