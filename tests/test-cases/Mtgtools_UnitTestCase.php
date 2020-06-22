<?php
declare(strict_types=1);
use Mtgtools\Mtgtools_Enqueue;
use Mtgtools\Interfaces\Mtg_Data_Source;
use Mtgtools\Symbols\Mana_Symbol;

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
     * Get mock Enqueue module
     */
    protected function get_mock_enqueue() : Mtgtools_Enqueue
    {
        $enqueue = $this->createMock( Mtgtools_Enqueue::class );
        return $enqueue;
    }

    /**
     * Get mock Mtg_Data_Source
     */
    protected function get_mock_mtg_data_source() : Mtg_Data_Source
    {
        return $this->createMock( Mtg_Data_Source::class );
    }

    /**
     * Get several mock Mana_Symbol objects
     */
    protected function get_mock_mana_symbols() : array
    {
        return [
            $this->get_mock_symbol(),
            $this->get_mock_symbol([ 'plaintext' => '{Q}', 'pattern' => '/\{Q\}/' ]),
        ];
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
        ], $args );

        $symbol = $this->createMock( Mana_Symbol::class );

        $symbol->method('is_valid')->willReturn( $args['is_valid'] );
        $symbol->method('get_pattern')->willReturn( $args['pattern'] );
        $symbol->method('get_plaintext')->willReturn( $args['plaintext'] );
        $symbol->method('get_css_class')->willReturn( $args['css_class'] );
        $symbol->method('get_english_phrase')->willReturn( $args['english_phrase'] );
        $symbol->method('get_svg_uri')->willReturn( $args['svg_uri'] );

        return $symbol;
    }

}   // End of class