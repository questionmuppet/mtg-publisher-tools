<?php
declare(strict_types=1);
use Mtgtools\Scryfall\Scryfall_Request_Factory;
use Mtgtools\Scryfall\Requests;

class Scryfall_Request_FactoryTest extends Mtgtools_UnitTestCase
{
    /**
     * Factory class
     */
    private $factory;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->factory = new Scryfall_Request_Factory();
    }

    /**
     * TEST: Can get card request
     */
    public function testCanGetCardRequest() : void
    {
        $request = $this->factory->create_request([
            'type' => 'card',
            'endpoint' => 'cards/named?fuzzy=aust+com',
        ]);

        $this->assertInstanceOf( Requests\Scryfall_Object_Request::class, $request );
    }

    /**
     * TEST: Can get list request
     */
    public function testCanGetListRequest() : void
    {
        $request = $this->factory->create_request([
            'type' => 'list',
            'endpoint' => 'symbology',
        ]);

        $this->assertInstanceOf( Requests\Scryfall_List_Request::class, $request );
    }

}   // End of class