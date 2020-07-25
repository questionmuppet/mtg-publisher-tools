<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mtgtools\Sources\Scryfall\Scryfall_Data_Source;
use Mtgtools\Sources\Scryfall\Services;
use Mtgtools\Cards\Magic_Card;

class Scryfall_Data_Source_Test extends TestCase
{
    /**
     * Scryfall source object
     */
    private $scryfall;

    /**
     * Dependencies
     */
    private $symbols;
    private $cards;

    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();
        $this->symbols = $this->createMock( Services\Scryfall_Symbols::class );
        $this->cards = $this->createMock( Services\Scryfall_Cards::class );
        $this->scryfall = new Scryfall_Data_Source( $this->symbols, $this->cards );
    }

    /**
     * TEST: Can get image types
     */
    public function testCanGetImageTypes() : void
    {
        $types = $this->scryfall->get_image_types();

        $this->assertIsArray( $types );
    }

    /**
     * TEST: Can get display name
     */
    public function testCanGetDisplayName() : void
    {
        $name = $this->scryfall->get_display_name();

        $this->assertIsString( $name );
    }

    /**
     * TEST: Can get documentation uri
     */
    public function testCanGetDocumentationUri() : void
    {
        $url = $this->scryfall->get_documentation_uri();

        $this->assertIsString( $url );
    }

    /**
     * TEST: Can get mana symbols
     */
    public function testCanGetManaSymbols() : void
    {
        $symbols = $this->scryfall->get_mana_symbols();

        $this->assertIsArray( $symbols );
    }

    /**
     * TEST: Can fetch a card matching filters
     */
    public function testCanFetchCardMatchingFilters() : void
    {
        $card = $this->scryfall->fetch_card([]);

        $this->assertInstanceOf( Magic_Card::class, $card );
    }

}   // End of class