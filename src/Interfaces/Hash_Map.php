<?php
/**
 * Hash_Map
 * 
 * Exposes a set of objects as key-hash pairs for db comparison
 */

namespace Mtgtools\Interfaces;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

interface Hash_Map
{
    /**
     * Get hash map
     * 
     * @return array Associative array of "key" => "hash"
     */
    public function get_map() : array;

}   // End of interface