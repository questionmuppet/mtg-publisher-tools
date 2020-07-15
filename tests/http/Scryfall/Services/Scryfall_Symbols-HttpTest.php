<?php
declare(strict_types=1);

use Mtgtools\Scryfall\Services\Scryfall_Symbols;
use Mtgtools\Symbols\Mana_Symbol;

class Scryfall_Symbols_HttpTest extends Mtgtools_UnitTestCase
{
    /**
     * Data source object
     */
    private $symbols;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->symbols = new Scryfall_Symbols();
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
        $result = $this->symbols->get_all_symbols();

        $this->assertIsArray( $result );
        $this->assertContainsOnlyInstancesOf( Mana_Symbol::class, $result );
    }

}   // End of class