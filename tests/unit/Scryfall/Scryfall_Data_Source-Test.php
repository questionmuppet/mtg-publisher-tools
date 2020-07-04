<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mtgtools\Scryfall\Scryfall_Data_Source;

class Scryfall_Data_Source_Test extends TestCase
{
    /**
     * Scryfall source object
     */
    private $scryfall;

    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();
        $this->scryfall = new Scryfall_Data_Source();
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

}   // End of class