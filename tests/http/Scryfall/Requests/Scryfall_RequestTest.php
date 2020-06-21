<?php
declare(strict_types=1);
use Mtgtools\Scryfall\Requests\Scryfall_Request;
use Mtgtools\Exceptions\Api as Exceptions;

class Scryfall_RequestTest extends Mtgtools_UnitTestCase
{
    /**
     * TEST: Can fetch data from API
     */
    public function testCanFetchApiData() : array
    {
        $request = $this->create_request([
            'expects'  => 'card',
            'endpoint' => 'cards/e9d5aee0-5963-41db-a22b-cfea40a967a3',
        ]);

        $card = $request->get_data();

        $this->assertIsArray( $card );

        return $card;
    }

    /**
     * TEST: Api data contains correct fields
     * 
     * @depends testCanFetchApiData
     */
    public function testApiDataContainsCorrectFields( array $card ) : void
    {
        $this->assertEquals( 77240,          $card['mtgo_id'] ?? '' );
        $this->assertEquals( 'Dusk // Dawn', $card['name'] ?? '' );
    }

    /**
     * TEST: Bad request throws ApiStatusException
     * 
     * @depends testCanFetchApiData
     */
    public function testBadRequestThrowsApiStatusException() : void
    {
        $request = $this->create_request([
            'expects'  => 'card',
            'endpoint' => 'cards/search?q=is%3Aslick+cmc%3Ecmc',
        ]);

        $this->expectException( Exceptions\ApiStatusException::class );

        $request->get_data();
    }

    /**
     * TEST: Wrong response type throws ScryfallDataException
     * 
     * @depends testCanFetchApiData
     */
    public function testWrongResponseTypeThrowsScryfallDataException() : void
    {
        $request = $this->create_request([
            'expects'  => 'card',
            'endpoint' => 'sets/mmq',
        ]);

        $this->expectException( Exceptions\ScryfallDataException::class );

        $request->get_data();
    }

    /**
     * Create request object
     */
    private function create_request( array $args ) : Scryfall_Request
    {
        $args = array_merge([
            'expects'  => '',
            'endpoint' => '',
        ], $args );
        return new Scryfall_Request( $args );
    }

}   // End of class