<?php
declare(strict_types=1);

use Mtgtools\Scryfall\Services\Scryfall_Cards;
use Mtgtools\Cards\Magic_Card;
use Mtgtools\Exceptions\Sources\Scryfall as Exceptions;

class Scryfall_Cards_HttpTest extends Mtgtools_UnitTestCase
{
    /**
     * Test-card attributes
     */
    const UUID = '3faa8c5e-9e1b-4cee-b322-a033bf33dcbc';
    const NAME = 'Hymn to Tourach';
    const SET_CODE = 'ema';
    const COLLECTOR_NUMBER = '92';

    /**
     * Alternative set code
     */
    const ALT_SET_CODE = 'fem';
    
    /**
     * Languages
     */
    const LANG_EN = 'en';
    const LANG_JA = 'ja';

    /**
     * Dfc
     */
    const DFC_FRONT = 'Delver of Secrets';
    const DFC_BACK = 'Insectile Aberration';

    /**
     * Image types
     */
    const NUM_TYPES = 6;

    /**
     * Service class
     */
    private $cards;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->cards = new Scryfall_Cards();
    }

    /**
     * ---------------
     *   S E A R C H
     * ---------------
     */

    /**
     * TEST: Can fetch card by uuid
     */
    public function testCanFetchCardByUuid() : Magic_Card
    {
        $card = $this->cards->fetch_card_by_filters([ 'uuid' => self::UUID ]);

        $this->assertEquals( self::NAME, $card->get_name() );

        return $card;
    }

    /**
     * TEST: Can fetch card by collector number
     */
    public function testCanFetchCardByCollectorNumber() : void
    {
        $card = $this->cards->fetch_card_by_filters([
            'set_code' => self::SET_CODE,
            'collector_number' => self::COLLECTOR_NUMBER,
        ]);

        $this->assertEquals( self::NAME, $card->get_name() );
    }

    /**
     * TEST: Can fetch card by collector number with language
     * 
     * @depends testCanFetchCardByCollectorNumber
     */
    public function testCanFetchCardByCollectorNumberWithLanguage() : void
    {
        $card = $this->cards->fetch_card_by_filters([
            'set_code' => self::SET_CODE,
            'collector_number' => self::COLLECTOR_NUMBER,
            'language' => self::LANG_JA,
        ]);

        $this->assertEquals( self::NAME, $card->get_name() );
        $this->assertEquals( self::LANG_JA, $card->get_language() );
    }

    /**
     * TEST: Can fetch card by name
     */
    public function testCanFetchCardByName() : void
    {
        $card = $this->cards->fetch_card_by_filters([ 'name' => self::NAME ]);

        $this->assertEquals( self::NAME, $card->get_name() );
    }

    /**
     * TEST: Can fetch card by name with set
     * 
     * @depends testCanFetchCardByName
     */
    public function testCanFetchCardByNameWithSet() : void
    {
        $card = $this->cards->fetch_card_by_filters([
            'name' => self::NAME,
            'set_code' => self::ALT_SET_CODE
        ]);

        $this->assertEquals( self::NAME, $card->get_name() );
        $this->assertEquals( self::ALT_SET_CODE, $card->get_set_code() );
    }

    /**
     * TEST: Can fetch front face of dfc
     * 
     * @depends testCanFetchCardByName
     */
    public function testCanFetchFrontFaceOfDfc() : void
    {
        $card = $this->cards->fetch_card_by_filters([
            'name' => self::DFC_FRONT,
        ]);

        $this->assertEquals( self::DFC_FRONT, $card->get_name() );
    }

    /**
     * TEST: Can fetch back face of dfc
     * 
     * @depends testCanFetchCardByName
     */
    public function testCanFetchBackFaceOfDfc() : void
    {
        $card = $this->cards->fetch_card_by_filters([
            'name' => self::DFC_BACK,
        ]);

        $this->assertEquals( self::DFC_BACK, $card->get_name() );
    }

    /**
     * ---------------
     *   I M A G E S
     * ---------------
     */

    /**
     * Single-faced card contains image uris
     * 
     * @depends testCanFetchCardByUuid
     */
    public function testSimpleCardContainsImageUris( Magic_Card $card ) : void
    {
        $this->assertCount( self::NUM_TYPES, $card->get_images(), 'Failed to assert that a Magic Card returned by Scryfall contained the right number of image uris.' );
    }

    /**
     * Double-faced card contains image uris
     * 
     * @depends testSimpleCardContainsImageUris
     * @depends testCanFetchCardByName
     */
    public function testDfcCardContainsImageUris() : void
    {
        $card = $this->cards->fetch_card_by_filters([ 'name' => 'Delver of Secrets' ]);

        $this->assertCount( self::NUM_TYPES, $card->get_images(), 'Failed to assert that a double-faced card returned by Scryfall contained the right number of image uris.' );
    }

    /**
     * -----------------------
     *   E X C E P T I O N S
     * -----------------------
     */

    /**
     * TEST: Fetching with invalid search scheme throws ScryfallParameterException
     */
    public function testFetchingWithInvalidSearchSchemeThrowsScryfallParameterException() : void
    {
        $this->expectException( Exceptions\ScryfallParameterException::class );

        $this->cards->fetch_card_by_filters([
            'irrelevant_key' => 'blah'
        ]);
    }

    /**
     * TEST: Id search with no results throws ScryfallApiException
     * 
     * @depends testCanFetchCardByUuid
     */
    public function testIdSearchWithNoResultsThrowsScryfallApiException() : void
    {
        $this->expectException( Exceptions\ScryfallApiException::class );

        $this->cards->fetch_card_by_filters([
            'uuid' => 'invalid_scryfall_id_string'
        ]);
    }

    /**
     * TEST: Collector number search with no results throws ScryfallApiException
     * 
     * @depends testCanFetchCardByCollectorNumber
     */
    public function testCollectorNumberSearchWithNoResultsThrowsScryfallApiException() : void
    {
        $this->expectException( Exceptions\ScryfallApiException::class );

        $this->cards->fetch_card_by_filters([
            'set_code' => 'invalid_set',
            'collector_number' => 'invalid_number',
        ]);
    }

    /**
     * TEST: Name search with no results throws ScryfallApiException
     * 
     * @depends testCanFetchCardByName
     */
    public function testNameSearchWithNoResultsThrowsScryfallApiException() : void
    {
        $this->expectException( Exceptions\ScryfallApiException::class );

        $this->cards->fetch_card_by_filters([
            'name' => 'nonexistent card name',
        ]);
    }

}   // End of class