<?php
declare(strict_types=1);

use Mtgtools\Cards\Magic_Card;
use Mtgtools\Cards\Image_Uri;
use Mtgtools\Exceptions\Cache as Exceptions;

class Magic_Card_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const UUID = 'xxxxx';
    const NAME = 'Stoneforge Mystic';
    const SET_CODE = 'WWK';
    const LANGUAGE = 'English';
    const VARIANT = 'A normal variant';
    const URI = 'https://www.example.com/image.svg';

    /**
     * Dependencies
     */
    private $images;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->images = [
            'small' => $this->createMock( Image_Uri::class ),
            'large' => $this->createMock( Image_Uri::class ),
        ];
    }

    /**
     * TEST: Invalid image in constructor throws DomainException
     */
    public function testInvalidImageInConstructorThrowsDomainException() : void
    {
        $this->images['bad'] = 'A malformed string uri';

        $this->expectException( \DomainException::class );

        $card = $this->create_card();
    }

    /**
     * TEST: Can get public properties
     */
    public function testCanGetPublicProperties() : void
    {
        $card = $this->create_card();

        $this->assertEquals( self::UUID, $card->get_uuid(), "Could not retrieve public property 'uuid'." );
        $this->assertEquals( self::NAME, $card->get_name(), "Could not retrieve public property 'name'." );
        $this->assertEquals( self::SET_CODE, $card->get_set_code(), "Could not retrieve public property 'set_code'." );
        $this->assertEquals( self::LANGUAGE, $card->get_language(), "Could not retrieve public property 'language'." );
        $this->assertEquals( self::VARIANT, $card->get_variant(), "Could not retrieve public property 'variant'." );
        $this->assertIsArray( $card->get_images(), "Could not retrieve public property 'images'." );
    }

    /**
     * TEST: Can get valid image uri by type
     * 
     * @depends testCanGetPublicProperties
     */
    public function testCanGetValidImageUriByType() : void
    {
        $this->images['large']->method('get_uri')->willReturn( self::URI );
        $card = $this->create_card();

        $uri = $card->get_image_uri( 'large' );

        $this->assertEquals( self::URI, $uri );
    }

    /**
     * TEST: Request for missing uri throws MissingDataException
     * 
     * @depends testCanGetValidImageUriByType
     */
    public function testRequestForMissingUriThrowsMissingDataException() : void
    {
        $card = $this->create_card();

        $this->expectException( Exceptions\MissingDataException::class );

        $uri = $card->get_image_uri( 'invalid_type' );
    }

    /**
     * TEST: Request for expired uri throws ExpiredDataException
     * 
     * @depends testCanGetValidImageUriByType
     */
    public function testRequestForExpiredUriThrowsExpiredDataException() : void
    {
        $this->images['small']->method('is_expired')->willReturn( true );
        $card = $this->create_card();

        $this->expectException( Exceptions\ExpiredDataException::class );

        $uri = $card->get_image_uri( 'small' );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Create card object
     */
    private function create_card( array $args = [] ) : Magic_Card
    {
        $args = array_replace([
            'uuid' => self::UUID,
            'name' => self::NAME,
            'set_code' => self::SET_CODE,
            'language' => self::LANGUAGE,
            'variant' => self::VARIANT,
            'images' => $this->images,
        ], $args );
        return new Magic_Card( $args );
    }

}   // End of class