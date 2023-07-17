<?php
declare(strict_types=1);

use Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase;
use Mtgtools\Symbols\Symbols_Hash_Map;

class Symbols_Hash_Map_Test extends Mtgtools_UnitTestCase
{
    /**
     * Hash map
     */
    private $hash_map;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->hash_map = new Symbols_Hash_Map();
    }

    /**
     * TEST: Can get hash map
     */
    public function testCanGetHashMap() : void
    {
        $hashes = $this->hash_map->get_map();

        $this->assertIsArray( $hashes );
    }

    /**
     * TEST: Can add Mana_Symbol items
     * 
     * @depends testCanGetHashMap
     */
    public function testCanAddSymbols() : void
    {
        $symbols = $this->get_mock_symbols(2);

        $this->hash_map->add_records( $symbols );
        $hashes = $this->hash_map->get_map();

        $this->assertCount( 2, $hashes );
    }

    /**
     * TEST: Adding invalid symbol throws TypeError
     * 
     * @depends testCanAddSymbols
     */
    public function testAddingInvalidSymbolThrowsTypeError() : void
    {
        $symbols = $this->get_mock_symbols(2);
        $symbols[] = 'A malformed string item';

        $this->expectException( \TypeError::class );

        $this->hash_map->add_records( $symbols );
    }

    /**
     * TEST: Adding duplicate symbol throws LogicException
     * 
     * @depends testCanAddSymbols
     */
    public function testAddingDuplicateSymbolThrowsLogicException() : void
    {
        $symbols = $this->get_mock_symbols(1);
        $this->hash_map->add_records( $symbols );

        $this->expectException( \LogicException::class );

        $this->hash_map->add_records( $symbols );
    }

}   // End of class