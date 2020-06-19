<?php
declare(strict_types=1);
use \Mtgtools\Symbols\Mana_Symbol;

abstract class Mtgtools_UnitTestCase extends WP_UnitTestCase
{
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
     *   P R O D U C E R S
     * ---------------------
     */

    /**
     * Get mock Mana_Symbol object
     */
    protected function get_mock_symbol( array $args = [] ) : Mana_Symbol
    {
        $args = array_merge([
            'is_valid'       => true,
            'pattern'        => '{T}',
            'english_phrase' => 'tap this permanent',
            'svg_uri'        => 'https://img.scryfall.com/symbology/T.svg',
        ], $args );

        $symbol = $this->createMock( Mana_Symbol::class );

        $symbol->method('is_valid')->willReturn( $args['is_valid'] );
        $symbol->method('get_pattern')->willReturn( $args['pattern'] );
        $symbol->method('get_english_phrase')->willReturn( $args['english_phrase'] );
        $symbol->method('get_svg_uri')->willReturn( $args['svg_uri'] );

        return $symbol;
    }

}   // End of class