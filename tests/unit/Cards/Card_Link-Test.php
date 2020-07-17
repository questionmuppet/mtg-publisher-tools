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
    const URI = 'https://www.example.com/image.svg';
    const CONTENT = 'Stoneforge Mystic';
    const ALTERNATE_NAME = 'Nahiri the Lithomancer';
    const FILTERS = [
        'id' => 'fake_1',
        'set' => 'fake_2',
        'number' => 'fake_3'
    ];
    
    /**
     * TEST: Content is used as name filter by default
     */
    public function testContentUsedAsNameFilterByDefault() : void
    {
        $link = $this->create_link();
        
        $filters = $link->get_filters();

        $this->assertEquals( self::CONTENT, $filters['name'] );
    }

    /**
     * TEST: Name filter can be overridden in constructor
     * 
     * @depends testContentUsedAsNameFilterByDefault
     */
    public function testNameFilterCanBeOverriddenInConstructor() : void
    {
        $filters = self::FILTERS;
        $filters['name'] = self::ALTERNATE_NAME;
        $link = $this->create_link([ 'filters' => $filters ]);

        $parsed = $link->get_filters();

        $this->assertEquals( self::ALTERNATE_NAME, $parsed['name'] );
    }

    /**
     * TEST: Markup is formed correctly
     */
    public function testMarkupIsFormedCorrectly() : void
    {
        $link = $this->create_link();
        $link->set_href( self::URI );

        $html = $link->get_markup();

        $selector = $this->generate_selector([
            'tag' => 'a',
            'classes' => self::LINK_CLASS
        ]);
        $this->assertContainsSelector( $selector, $html, 'Did not find an <a> element with the expected classes in the link markup.' );
        $this->assertElementContains( self::CONTENT, $selector, $html, 'Could not find the content string in the link markup.' );
        $this->assertHasElementWithAttributes( [ 'href' => self::URI ], $html, 'Did not find an "href" attribute with the expected value in the link markup.' );
    }

    /**
     * TEST: Filters are converted to data-attributes
     * 
     * @depends testNameFilterCanBeOverriddenInConstructor
     * @depends testMarkupIsFormedCorrectly
     */
    public function testFiltersAreConvertedToDataAttributes() : void
    {
        $filters = self::FILTERS;
        $filters['name'] = self::ALTERNATE_NAME;
        $link = $this->create_link([ 'filters' => $filters ]);
        
        $html = $link->get_markup();

        $atts = [];
        foreach ( $filters as $key => $value )
        {
            $atts[ "data-{$key}" ] = $value;
        }
        $this->assertHasElementWithAttributes( $atts, $html, 'Failed to assert that the filters are converted to data-attributes in the markup.' );
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
            'filters' => self::FILTERS,
            'content' => self::CONTENT,
        ], $args );
        return new Card_Link( $args );
    }

    /**
     * Get selector by element and classes
     */
    private function generate_selector( array $args ) : string
    {
        $args = array_replace([
            'tag' => '',
            'classes' => [],
        ], $args );
        $parts = (array) $args['classes'];
        array_unshift( $parts, $args['tag'] );
        return implode( '.', array_filter( $parts ) );
    }

}   // End of class