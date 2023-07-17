<?php
declare(strict_types=1);

use Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase;
use Mtgtools\Symbols\Mana_Symbol;
use Mtgtools\Wp_Task_Library;

class Mana_Symbol_WpTest extends Mtgtools_UnitTestCase
{
    /**
     * TEST: Can get correct HTML markup
     */
    public function testCanGetCorrectMarkup() : void
    {
        $symbol = $this->create_symbol();
        $library = new Wp_Task_Library();

        $html = $symbol->get_markup( $library );

        $this->assertHasElementWithAttributes(
            [
                'alt' => 'Tap this permanent',
                'src' => 'https://img.scryfall.com/symbology/T.svg',
            ],
            $html,
            'Could not find an element with the correct attributes in the markup.'
        );
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