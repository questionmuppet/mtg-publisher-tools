<?php
declare(strict_types=1);

use Mtgtools\Symbols\Mana_Symbol;

class Mana_SymbolTest extends Mtgtools_UnitTestCase
{
    /**
     * TEST: Empty plaintext
     */
    public function testSymbolWithEmptyPlaintextIsInvalid() : void
    {
        $symbol = $this->create_symbol([
            'plaintext'      => '',
            'english_phrase' => '',
            'svg_uri'        => '',
        ]);
        
        $this->assertFalse( $symbol->is_valid() );
    }

    /**
     * TEST: Can get public properties
     */
    public function testCanGetPublicProperties() : void
    {
        $symbol = $this->create_symbol();

        $this->assertIsString( $symbol->get_plaintext(), "Failed to get public property 'plaintext'." );
        $this->assertIsString( $symbol->get_css_class(), "Failed to get public property 'css_class'." );
        $this->assertIsString( $symbol->get_english_phrase(), "Failed to get public property 'english_phrase'." );
        $this->assertIsString( $symbol->get_svg_uri(), "Failed to get public property 'svg_uri'." );
    }

    /**
     * TEST: get_pattern returns valid regex
     */
    public function testGetPatternReturnsValidRegex() : void
    {
        $symbol = $this->create_symbol();

        $result = $symbol->get_pattern();

        $this->assertIsValidRegex( $result );
    }

    /**
     * TEST: get_pattern returns valid regex using '/' character
     * 
     * @depends testGetPatternReturnsValidRegex
     */
    public function testGetPatternWorksWithSlashChar() : void
    {
        $symbol = $this->create_symbol([ 'plaintext' => '{W/P}' ]);

        $pattern = $symbol->get_pattern();
        $match = boolval( preg_match( $pattern, '{W/P}' ) );

        $this->assertTrue( $match );
    }

    /**
     * TEST: Update hash contains correct elements
     * 
     * @depends testCanGetPublicProperties
     */
    public function testUpdateHashContainsCorrectElements() : void
    {
        $symbol = $this->create_symbol();
        $elements = [
            $symbol->get_plaintext(),
            $symbol->get_english_phrase(),
            $symbol->get_svg_uri(),
        ];
        $expected_hash = md5( implode( '|', $elements ) );

        $hash = $symbol->get_update_hash();

        $this->assertEquals( $expected_hash, $hash, 'The update hash for a mana symbol does not match the expected hash.' );
    }

    /**
     * Create mana symbol
     */
    private function create_symbol( array $args = [] ) : Mana_Symbol
    {
        $args = array_merge([
            'plaintext'      => '{T}',
            'english_phrase' => 'Tap this permanent',
            'svg_uri'        => 'https://img.scryfall.com/symbology/T.svg',
        ], $args );
        return new Mana_Symbol( $args );
    }

}   // End of class