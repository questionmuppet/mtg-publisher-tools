<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Images;
use Mtgtools\Interfaces\Mtg_Data_Source;
use Mtgtools\Mtgtools_Plugin;
use Mtgtools\Cards\Card_Db_Ops;
use Mtgtools\Cards\Magic_Card;
use Mtgtools\Exceptions\Db\DbException;

class Mtgtools_Images_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const TEST_URI = 'https://www.example.com/image.svg';

    /**
     * Images module
     */
    private $images;

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
        $this->images = new Mtgtools_Images( $this->db_ops, $this->source, $this->plugin );
    }

    /**
     * TEST: Can add hooks
     */
    public function testCanAddHooks() : void
    {
        $result = $this->images->add_hooks();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can get card link
     */
    public function testCanGetCardLink() : void
    {
        // Use live settings module
        $settings = Mtgtools\Mtgtools_Plugin::get_instance()->settings();
        $this->plugin->method('settings')->willReturn( $settings );
        $html = $this->images->add_card_link( [], 'Fake card' );

        $this->assertIsString( $html );
    }

    /**
     * TEST: Can get image uri from db
     */
    public function testCanGetImageUriFromDb() : void
    {
        $this->db_ops->method('find_card')->willReturn( $this->get_mock_card() );
        
        $uri = $this->images->find_image_uri( [], '' );
        
        $this->assertEquals( self::TEST_URI, $uri );
    }
    
    /**
     * TEST: Can get image uri remotely
     * 
     * @depends testCanGetImageUriFromDb
     */
    public function testCanGetImageUriRemotely() : void
    {
        $this->db_ops->method('find_card')->willThrowException( new DbException( "Encountered a database error." ) );
        $this->source->method('fetch_card')->willReturn( $this->get_mock_card() );

        $uri = $this->images->find_image_uri( [], '' );

        $this->assertEquals( self::TEST_URI, $uri );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */
    
    /**
     * Get mock Magic_Card with image uri
     */
    private function get_mock_card() : Magic_Card
    {
        $card = $this->createMock( Magic_Card::class );
        $card->method('get_image_uri')->willReturn( self::TEST_URI );
        return $card;
    }

}   // End of class