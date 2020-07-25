<?php
declare(strict_types=1);

use Mtgtools\Cards;
use Mtgtools\Db\Services\Card_Db_Ops;
use Mtgtools\Sources\Mtg_Data_Source;
use Mtgtools\Mtgtools_Plugin;
use Mtgtools\Exceptions\Mtg;
use Mtgtools\Exceptions\Sources\MtgSourceException;

class Card_Cache_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const IMG_VALID = 'valid';
    const IMG_MISSING = 'missing';
    const IMG_EXPIRED = 'expired';
    const FILTERS = [
        'id' => 'valid_card_id',
    ];

    /**
     * SUT object
     */
    private $cache;

    /**
     * Dependencies
     */
    private $db_ops;
    private $source;
    private $plugin;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->db_ops = $this->createMock( Card_Db_Ops::class );
        $this->source = $this->createMock( Mtg_Data_Source::class );
        $this->plugin = $this->createMock( Mtgtools_Plugin::class );
        $this->cache = new Cards\Card_Cache( $this->db_ops, $this->source, $this->plugin );
    }

    /**
     * TEST: Locating card with invalid filters throws MtgParameterException
     */
    public function testLocatingCardWithInvalidFiltersThrowsMtgParameterException() : void
    {
        $this->expectException( Mtg\MtgParameterException::class );

        $this->cache->locate_card([]);
    }

    /**
     * TEST: Can retrieve card from cache
     */
    public function testCanRetrieveCardFromCache() : void
    {
        $card = $this->cache->locate_card( self::FILTERS );

        $this->assertInstanceOf( Cards\Magic_Card::class, $card );
    }

    /**
     * TEST: Can retrieve card using name
     * 
     * @depends testCanRetrieveCardFromCache
     */
    public function testCanRetrieveCardUsingName() : void
    {
        $card = $this->cache->locate_card([ 'name' => 'Stoneforge Mystic' ]);

        $this-> assertInstanceOf( Cards\Magic_Card::class, $card );
    }

    /**
     * TEST: Can retrieve card using set + number
     * 
     * @depends testCanRetrieveCardFromCache
     */
    public function testCanRetrieveCardUsingCollectorNumber() : void
    {
        $card = $this->cache->locate_card([
            'set' => 'WWK',
            'number' => 20,
            'language' => 'en',
        ]);

        $this-> assertInstanceOf( Cards\Magic_Card::class, $card );
    }

    /**
     * TEST: Can retrieve card with required image type
     * 
     * @depends testCanRetrieveCardFromCache
     */
    public function testCanRetrieveCardWithRequiredImageType() : void
    {
        $this->db_ops->method('find_card')->willReturn( $this->get_mock_card() );

        $card = $this->cache->locate_card( self::FILTERS, self::IMG_VALID );

        $this->assertInstanceOf( Cards\Magic_Card::class, $card );
    }

    /**
     * TEST: Missing image in cache triggers remote fetch
     * 
     * @depends testCanRetrieveCardWithRequiredImageType
     */
    public function testMissingImageInCacheTriggersRemoteFetch() : void
    {
        $this->db_ops->method('find_card')->willReturn( $this->get_mock_card() );
        $this->source->expects( $this->once() )->method('fetch_card');

        $this->cache->locate_card( self::FILTERS, self::IMG_MISSING );
    }

    /**
     * TEST: Expired image in cache triggers remote fetch
     * 
     * @depends testMissingImageInCacheTriggersRemoteFetch
     */
    public function testExpiredImageInCacheTriggersRemoteFetch() : void
    {
        $this->db_ops->method('find_card')->willReturn( $this->get_mock_card() );
        $this->source->expects( $this->once() )->method('fetch_card');

        $card = $this->cache->locate_card( self::FILTERS, self::IMG_EXPIRED );
        $this->assertInstanceOf( Cards\Magic_Card::class, $card, 'Failed to assert that a remote search was able to retreive a Magic card.' );
    }

    /**
     * TEST: Failure on remote fetch throws MtgFetchException
     * 
     * @depends testExpiredImageInCacheTriggersRemoteFetch
     */
    public function testFailureOnRemoteFetchThrowsMtgFetchException() : void
    {
        $this->db_ops->method('find_card')->willReturn( $this->get_mock_card() );
        $this->source->method('fetch_card')->willThrowException( new MtgSourceException( "Card matching filters not found!" ) );

        $this->expectException( Mtg\MtgFetchException::class );

        $card = $this->cache->locate_card( self::FILTERS, self::IMG_EXPIRED );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Get mock card with images
     */
    private function get_mock_card() : Cards\Magic_Card
    {
        $card = $this->createMock( Cards\Magic_Card::class );
        $card->method('has_image')->will(
            $this->returnValueMap([
                [ self::IMG_VALID, true ],
                [ self::IMG_MISSING, false ],
                [ self::IMG_EXPIRED, true ],
            ])
        );
        $card->method('get_image')->will(
            $this->returnValueMap([
                [ self::IMG_VALID, $this->get_mock_image() ],
                [ self::IMG_EXPIRED, $this->get_mock_image([ 'expired' => true ]) ],
            ])
        );
        return $card;
    }

    /**
     * Get mock image
     */
    private function get_mock_image( $args = [] ) : Cards\Image_Uri
    {
        $args = array_replace([
            'expired' => false
        ], $args );
        $image = $this->createMock( Cards\Image_Uri::class );
        $image->method('is_expired')->willReturn( $args['expired'] );
        return $image;
    }

}   // End of class