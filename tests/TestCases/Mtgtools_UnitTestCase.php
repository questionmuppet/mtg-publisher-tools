<?php
declare(strict_types=1);

namespace Mtgtools\Tests\TestCases;

use WP_UnitTestCase;
use SteveGrunwell\PHPUnit_Markup_Assertions\MarkupAssertionsTrait;
use Mtgtools\Symbols\Mana_Symbol;

abstract class Mtgtools_UnitTestCase extends WP_UnitTestCase
{
    /**
     * Include markup assertions
     */
    use MarkupAssertionsTrait;

    /**
     * -----------------------
     *   A S S E R T I O N S
     * -----------------------
     */

    /**
     * Assert valid regex formatting
     */
    public function assertIsValidRegex( $value, $message = '' )
    {
        $match = @preg_match( $value, "" );
        $this->assertNotFalse( $match, $message );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Get specified number of mock symbols
     */
    protected function get_mock_symbols( int $count ) : array
    {
        $plaintexts = [ '{T}', '{Q}', '{X}', '{1}', '{15}', '{U}', '{W}', '{W/U}', '{2/U}', '{W/P}' ];
        $symbols = [];
        foreach ( array_slice( $plaintexts, 0, $count ) as $text )
        {
            $symbols[] = $this->get_mock_symbol([ 'plaintext' => $text ]);
        }
        return $symbols;
    }

    /**
     * Get mock Mana_Symbol object
     */
    protected function get_mock_symbol( array $args = [] ) : Mana_Symbol
    {
        $args = array_merge([
            'is_valid'       => true,
            'pattern'        => '/\{T\}/',
            'plaintext'      => '{T}',
            'css_class'      => 'mtg-symbol',
            'english_phrase' => 'Tap this permanent',
            'svg_uri'        => 'https://img.scryfall.com/symbology/T.svg',
            'markup'         => '',
        ], $args );

        $symbol = $this->createMock( Mana_Symbol::class );

        $symbol->method('is_valid')->willReturn( $args['is_valid'] );
        $symbol->method('get_pattern')->willReturn( $args['pattern'] );
        $symbol->method('get_plaintext')->willReturn( $args['plaintext'] );
        $symbol->method('get_css_class')->willReturn( $args['css_class'] );
        $symbol->method('get_english_phrase')->willReturn( $args['english_phrase'] );
        $symbol->method('get_svg_uri')->willReturn( $args['svg_uri'] );
        $symbol->method('get_markup')->willReturn( $args['markup'] );

        return $symbol;
    }
}
