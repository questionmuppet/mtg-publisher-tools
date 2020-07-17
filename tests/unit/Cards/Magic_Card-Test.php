<?php
declare(strict_types=1);

use Mtgtools\Cards\Magic_Card;
use Mtgtools\Cards\Image_Uri;

class Magic_Card_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const UUID = 'xxxxx';
    const NAME = 'Stoneforge Mystic';
    const SET_CODE = 'WWK';
    const LANGUAGE = 'English';
    const COLLECTOR_NUMBER = '42a';

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
        $this->assertEquals( self::COLLECTOR_NUMBER, $card->get_collector_number(), "Could not retrieve public property 'collector_number'." );
        $this->assertEquals( self::LANGUAGE, $card->get_language(), "Could not retrieve public property 'language'." );
        $this->assertCount( 2, $card->get_images(), "Failed to assert that a card contained the expected number of image uris." );
        $this->assertIsString( $card->get_name_with_edition(), "Could not retrieve human-readable name string with edition." );
        $this->assertIsString( $card->get_alt_text(), "Could not retreive alt-text string." );
    }
    
    /**
     * TEST: Can get image by type
     */
    public function testCanGetImageByType() : void
    {
        $card = $this->create_card();

        $image = $card->get_image( 'large' );

        $this->assertInstanceOf( Image_Uri::class, $image );
    }

    /**
     * TEST: Requesting a missing type returns default image
     * 
     * @depends testCanGetImageByType
     */
    public function testRequestingMissingImageReturnsDefault() : void
    {
        $card = $this->create_card();

        $image = $card->get_image( 'missing_type' );

        $this->assertInstanceOf( Image_Uri::class, $image );
    }

    /**
     * TEST: Requesting an image when none available throws UnexpectedValueException
     */
    public function testRequestingImageWhenNoneAvailableThrowsUnexpectedValueException() : void
    {
        $card = $this->create_card([ 'images' => [] ]);

        $this->expectException( \UnexpectedValueException::class );

        $card->get_image();
    }
    
    /**
     * Can check for presence of image
     */
    public function testCanCheckForImagePresence() : void
    {
        $card = $this->create_card();

        $this->assertTrue( $card->has_image( 'small' ) );
        $this->assertFalse( $card->has_image( 'invalid_type' ) );
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
            'collector_number' => self::COLLECTOR_NUMBER,
            'images' => $this->images,
        ], $args );
        return new Magic_Card( $args );
    }

}   // End of class