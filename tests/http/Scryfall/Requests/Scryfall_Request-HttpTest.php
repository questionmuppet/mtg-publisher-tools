<?php
declare(strict_types=1);
use Mtgtools\Scryfall\Requests\Scryfall_Request;
use Mtgtools\Exceptions\Api as Exceptions;

class Scryfall_Request_HttpTest extends Mtgtools_UnitTestCase
{
    /**
     * TEST: Can fetch API data using full url
     */
    public function testCanFetchFullUrl() : void
    {
        $request = $this->create_request([
            'full_url' => 'https://api.scryfall.com/sets/mmq',
            'expects'  => 'set',
        ]);

        $result = $request->get_data();

        $this->assertIsArray( $result );
    }

    /**
     * TEST: Can fetch API data using endpoint
     */
    public function testCanFetchEndpoint() : array
    {
        $request = $this->create_request([
            'endpoint' => 'cards/e9d5aee0-5963-41db-a22b-cfea40a967a3',
            'expects'  => 'card',
        ]);

        $card = $request->get_data();

        $this->assertIsArray( $card );

        return $card;
    }

    /**
     * TEST: Api data contains correct fields
     * 
     * @depends testCanFetchEndpoint
     */
    public function testApiDataContainsCorrectFields( array $card ) : void
    {
        $this->assertEquals( 77240,          $card['mtgo_id'] ?? '' );
        $this->assertEquals( 'Dusk // Dawn', $card['name'] ?? '' );
    }

    /**
     * TEST: Bad request throws ApiStatusException
     * 
     * @depends testCanFetchEndpoint
     */
    public function testBadRequestThrowsApiStatusException() : void
    {
        $request = $this->create_request([
            'endpoint' => 'cards/search?q=is%3Aslick+cmc%3Ecmc',
            'expects'  => 'card',
        ]);

        $this->expectException( Exceptions\ApiStatusException::class );

        $request->get_data();
    }

    /**
     * TEST: Wrong response type throws ScryfallDataException
     * 
     * @depends testCanFetchEndpoint
     */
    public function testWrongResponseTypeThrowsScryfallDataException() : void
    {
        $request = $this->create_request([
            'endpoint' => 'sets/mmq',
            'expects'  => 'card',
        ]);

        $this->expectException( Exceptions\ScryfallDataException::class );

        $request->get_data();
    }

    /**
     * Create request object
     */
    private function create_request( array $args ) : Scryfall_Request
    {
        return new Scryfall_Request( $args );
    }

}   // End of class