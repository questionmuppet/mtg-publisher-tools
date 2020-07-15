<?php
declare(strict_types=1);

use SteveGrunwell\PHPUnit_Markup_Assertions\MarkupAssertionsTrait;
use Mtgtools\Cards\Card_Link;
use Mtgtools\Mtgtools_Images;

class Card_Link_Test extends WP_UnitTestCase
{
    /**
     * Include markup assertions
     */
    use MarkupAssertionsTrait;

    /**
     * Constants
     */
    const LINK_CLASS = 'mtgtools-card-link';
    const AJAX_CLASS = 'is-ajax';
    const CONTENT = 'Stoneforge Mystic';
    const URI = 'https://www.example.com/image.svg';
    const FILTERS = [
        'uuid' => 'fake_1',
        'name' => 'fake_2',
        'set_code' => 'fake_3',
    ];

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
        $this->images = $this->createMock( Mtgtools_Images::class );
    }

    /**
     * TEST: Can get ajax markup
     */
    public function testCanGetAjaxMarkup() : void
    {
        $link = $this->create_link();

        $html = $link->get_markup();

        $selector = sprintf(
            'a.%s.%s',
            self::LINK_CLASS,
            self::AJAX_CLASS
        );
        $this->assertContainsSelector( $selector, $html, 'Did not find an <a> element with the expected classes in the link markup.' );
        $this->assertElementContains( self::CONTENT, $selector, $html, 'Could not find the content string in the link markup.' );
        $this->assertHasElementWithAttributes(
            [ 'data-name' => self::CONTENT ],
            $html,
            'Failed to assert that the name filter uses the content string by default.'
        );
    }
    
    /**
     * TEST: Can get markup with uri
     * 
     * @depends testCanGetAjaxMarkup
     */
    public function testCanGetMarkupWithUri() : void
    {
        $this->images->method('find_image_uri')->willReturn( self::URI );
        $link = $this->create_link([ 'is_ajax' => false ]);

        $html = $link->get_markup();

        $this->assertContainsSelector( 'a.' . self::LINK_CLASS, $html, 'Did not find an <a> element with the expected classes in the link markup.' );
        $this->assertNotContainsSelector( 'a.' . self::AJAX_CLASS, $html, 'Failed to assert that the ajax class was omitted when getting markup with uri.' );
        $this->assertHasElementWithAttributes( [ 'href' => self::URI ], $html, 'Did not find an "href" attribute with the expected value in the link markup.' );
    }

    /**
     * TEST: Optional filters are converted to data-attributes
     * 
     * @depends testCanGetAjaxMarkup
     */
    public function testOptionalFiltersAreConvertedToDataAttributes() : void
    {
        $link = $this->create_link([ 'filters' => self::FILTERS ]);
        
        $html = $link->get_markup();

        $attrs = [];
        foreach ( self::FILTERS as $key => $value )
        {
            $attrs[ "data-{$key}" ] = $value;
        }
        $this->assertHasElementWithAttributes( $attrs, $html, 'Failed to assert that optional filters are converted to data-attributes.' );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Create link
     */
    private function create_link( array $args = [] ) : Card_Link
    {
        $args = array_replace([
            'filters' => [],
            'content' => self::CONTENT,
            'is_ajax' => true,
        ], $args );
        return new Card_Link( $args, $this->images );
    }

}   // End of class