<?php
declare(strict_types=1);

use Mtgtools\Cards\Image_Uri;

class Image_Uri_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const CARD_UUID = 'xxxxx';
    const URI = 'https://www.example.com/image.svg';
    const TYPE = 'small';
    const CACHE_PERIOD = WEEK_IN_SECONDS;

    /**
     * TEST: Can get public properties
     */
    public function testCanGetPublicProperties() : void
    {
        $img = $this->create_image_uri();

        $this->assertEquals( self::CARD_UUID, $img->get_card_uuid(), "Could not retrieve public property 'card_uuid'." );
        $this->assertEquals( self::URI, $img->get_uri(), "Could not retrieve public property 'uri'." );
        $this->assertEquals( self::TYPE, $img->get_type(), "Could not retrieve public property 'type'." );
    }

    /**
     * TEST: Is not expired when within cache period
     */
    public function testIsNotExpiredWhenWithinCachePeriod() : void
    {
        $img = $this->create_image_uri([ 'cached' => $this->timetostr() ]);

        $expired = $img->is_expired();

        $this->assertFalse( $expired );
    }

    /**
     * TEST: Is expired when past cache period
     */
    public function testIsExpiredWhenPastCachePeriod() : void
    {
        $img = $this->create_image_uri([
            'cached' => $this->timetostr( time() - 2 * self::CACHE_PERIOD )
        ]);

        $expired = $img->is_expired();

        $this->assertTrue( $expired );
    }

    /**
     * TEST: Is expired with cache period of zero
     */
    public function testIsExpiredWithZeroCachePeriod() : void
    {
        $img = $this->create_image_uri([
            'cached' => $this->timetostr( time() - 1 ),     // One second ago
            'cache_period' => 0
        ]);

        $expired = $img->is_expired();

        $this->assertTrue( $expired );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Create image uri
     */
    private function create_image_uri( array $args = [] ) : Image_Uri
    {
        $args = array_replace([
            'card_uuid' => self::CARD_UUID,
            'uri' => self::URI,
            'type' => self::TYPE,
            'cached' => $this->timetostr(),
            'cache_period' => self::CACHE_PERIOD,
        ], $args );
        return new Image_Uri( $args );
    }

    /**
     * Convert timestamp to string
     */
    private function timetostr( int $timestamp = null ) : string
    {
        return date( DATE_RFC3339, $timestamp ?? time() );
    }

}   // End of class