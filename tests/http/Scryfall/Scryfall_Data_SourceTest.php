<?php
declare(strict_types=1);
use Mtgtools\Scryfall\Scryfall_Data_Source;
use Mtgtools\Symbols\Mana_Symbol;

class Scryfall_Data_SourceTest extends Mtgtools_UnitTestCase
{
    /**
     * Data source object
     */
    private $source;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->source = new Scryfall_Data_Source();
    }

    /**
     * -------------
     *   T E S T S
     * -------------
     */

    /**
     * TEST: Can get mana symbols
     */
    public function testCanGetManaSymbols() : void
    {
        $result = $this->source->get_mana_symbols();

        $this->assertIsArray( $result );
        $this->assertContainsOnlyInstancesOf( Mana_Symbol::class, $result );
    }

}   // End of class