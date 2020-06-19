<?php
declare(strict_types=1);
use Mtgtools\Symbols\Mana_Symbol;
use SteveGrunwell\PHPUnit_Markup_Assertions\MarkupAssertionsTrait;

class Mana_SymbolTest extends Mtgtools_UnitTestCase
{
    /**
     * Include markup assertions
     */
    use MarkupAssertionsTrait;

    /**
     * TEST: Empty plaintext
     */
    public function testSymbolWithEmptyPlaintextIsInvalid() : void
    {
        $symbol = new Mana_Symbol([
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
        $symbol = $this->get_default_symbol();

        $this->assertIsString( $symbol->get_plaintext(), "Failed to get public property 'plaintext'." );
        $this->assertIsString( $symbol->get_markup(), "Failed to get public property 'markup'." );
        $this->assertIsString( $symbol->get_english_phrase(), "Failed to get public property 'english_phrase'." );
        $this->assertIsString( $symbol->get_svg_uri(), "Failed to get public property 'svg_uri'." );
    }

    /**
     * TEST: Attributes in HTML markup
     */
    public function testMarkupHasCorrectAttributes() : void
    {
        $symbol = $this->get_default_symbol();

        $html = $symbol->get_markup();

        $this->assertHasElementWithAttributes(
            [
                'alt' => 'tap this permanent',
                'src' => 'https://img.scryfall.com/symbology/T.svg',
            ],
            $html
        );
    }

    /**
     * TEST: get_pattern returns valid regex
     */
    public function testGetPatternReturnsValidRegex() : void
    {
        $symbol = $this->get_default_symbol();

        $result = $symbol->get_pattern();

        $this->assertIsValidRegex( $result );
    }

    /**
     * Get default test symbol
     */
    private function get_default_symbol() : Mana_Symbol
    {
        return new Mana_Symbol([
            'plaintext'      => '{T}',
            'english_phrase' => 'tap this permanent',
            'svg_uri'        => 'https://img.scryfall.com/symbology/T.svg',
        ]);
    }

}   // End of class