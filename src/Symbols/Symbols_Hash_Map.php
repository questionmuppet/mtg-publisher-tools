<?php
/**
 * Symbols_Hash_Map
 * 
 * Exposes mana symbols as key-hash pairs for db comparison
 */

namespace Mtgtools\Symbols;
use Mtgtools\Interfaces\Hash_Map;

class Symbols_Hash_Map implements Hash_Map
{
    /**
     * Hashes, keyed by plaintext
     */
    private $hashes = [];

    /**
     * Get key-to-hash map
     */
    public function get_map() : array
    {
        return $this->hashes;
    }

    /**
     * Add items to hash map
     */
    public function add_records( array $items ) : void
    {
        foreach ( $items as $symbol )
        {
            $this->add_hash_pair( $symbol );
        }
    }

    /**
     * Create a new hash record from a Mana_Symbol
     */
    private function add_hash_pair( Mana_Symbol $symbol ) : void
    {
        $key = $symbol->get_plaintext();
        if ( array_key_exists( $key, $this->hashes ) )
        {
            throw new \LogicException( get_called_class() . " tried to add a duplicate key to the hash map. A record already exists for key '{$key}'." );
        }
        $this->hashes[ $key ] = $symbol->get_update_hash();
    }

}   // End of class