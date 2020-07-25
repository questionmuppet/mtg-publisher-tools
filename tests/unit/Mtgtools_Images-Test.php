<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Images;
use Mtgtools\Cards\Card_Cache;
use Mtgtools\Mtgtools_Plugin;

use Mtgtools\Exceptions\Mtg;
use Mtgtools\Exceptions\Admin_Post;

class Mtgtools_Images_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const IMG_TYPE = 'png';
    const FILTERS = [
        'id' => 'valid_card_id',
    ];

    /**
     * Images module
     */
    private $images;

    /**
     * Dependencies
     */
    private $card_cache;
    private $plugin;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->card_cache = $this->createMock( Card_Cache::class );
        $this->plugin = $this->createMock( Mtgtools_Plugin::class );
        $this->images = new Mtgtools_Images( $this->card_cache, self::IMG_TYPE, $this->plugin );
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
     * TEST: Can get lazy card link
     */
    public function testCanGetLazyCardLink() : void
    {
        $this->add_live_settings();
        update_option( 'mtgtools_lazy_fetch_images', true );
        
        $html = $this->images->add_card_link( [], 'Fake card' );
        
        $this->assertIsString( $html );
    }

    /**
     * TEST: Can get greedy card link
     */
    public function testCanGetGreedyCardLink() : void
    {
        $this->add_live_settings();
        update_option( 'mtgtools_lazy_fetch_images', false );
        
        $html = $this->images->add_card_link( [], 'Fake card' );
        
        $this->assertIsString( $html );
    }

    /**
     * TEST: Can get greedy card link when encountering a fetch error
     * 
     * @depends testCanGetGreedyCardLink
     */
    public function testCanGetGreedyCardLinkThroughFetchError() : void
    {
        $this->add_live_settings();
        update_option( 'mtgtools_lazy_fetch_images', false );
        $this->card_cache->method('locate_card')->willThrowException( new Mtg\MtgDataException( "Couldn't find card matching the specified filters." ) );

        $html = $this->images->add_card_link( [], 'Fake card' );
        
        $this->assertIsString( $html );
    }

    /**
     * TEST: Can get popup markup
     */
    public function testCanGetPopupMarkup() : void
    {
        $this->add_live_settings();

        $result = $this->images->get_popup_markup( self::FILTERS );

        $this->assertIsString( $result['transients']['popup'] );
    }

    /**
     * TEST: Bad request throws ParameterException
     */
    public function testBadRequestThrowsParameterException() : void
    {
        $this->add_live_settings();
        $this->card_cache->method('locate_card')->willThrowException( new Mtg\MtgParameterException( "Invalid filters provided." ) );

        $this->expectException( Admin_Post\ParameterException::class );

        $result = $this->images->get_popup_markup( self::FILTERS );
    }

    /**
     * TEST: Fetch error throws ExternalCallException
     */
    public function testFetchErrorThrowsExternalCallException() : void
    {
        $this->add_live_settings();
        $this->card_cache->method('locate_card')->willThrowException( new Mtg\MtgFetchException( "Couldn't find card matching the specified filters." ) );

        $this->expectException( Admin_Post\ExternalCallException::class );

        $result = $this->images->get_popup_markup( self::FILTERS );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */
    
    /**
     * Add live settings module to plugin
     */
    public function add_live_settings() : void
    {
        $manager = Mtgtools_Plugin::get_instance()->options_manager();
        $this->plugin->method('options_manager')->willReturn( $manager );
    }

}   // End of class