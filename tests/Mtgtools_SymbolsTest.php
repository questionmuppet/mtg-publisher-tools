<?php
declare(strict_types=1);
use SteveGrunwell\PHPUnit_Markup_Assertions\MarkupAssertionsTrait;
use Mtgtools\Mtgtools_Symbols;
use Mtgtools\Symbols\Symbol_Db_Ops;

class Mtgtools_SymbolsTest extends Mtgtools_UnitTestCase
{
    /**
     * Include markup assertions
     */
    use MarkupAssertionsTrait;
    
    /**
     * TEST: Can enqueue assets
     */
    public function testCanEnqueueAssets() : void
    {
        $db_ops = $this->get_mock_db_ops();
        $enqueue = $this->get_mock_enqueue();
        $symbols = new Mtgtools_Symbols( $db_ops, $enqueue );

        $result = $symbols->enqueue_assets();

        $this->assertNull( $result );
    }

    /**
     * TEST: Shortcode parser returns string
     */
    public function testParseManaSymbolsReturnsString() : string
    {
        $db_ops = $this->get_mock_db_ops();
        $db_ops->method('get_mana_symbols')->willReturn( $this->get_mock_mana_symbols() );
        $enqueue = $this->get_mock_enqueue();
        $symbols = new Mtgtools_Symbols( $db_ops, $enqueue );

        $result = $symbols->parse_mana_symbols( [], "{T}: Do some biz; {Q}: Do some other biz" );

        $this->assertIsString( $result );

        return is_string( $result ) ? $result : '';
    }

    /**
     * TEST: Correct attributes appear in shortcode markup
     * 
     * @depends testParseManaSymbolsReturnsString
     */
    public function testParseManaSymbolsReturnsCorrectMarkup( string $html ) : void
    {
        $this->assertHasElementWithAttributes(
            [
                'alt' => 'Tap this permanent',
                'src' => 'https://img.scryfall.com/symbology/T.svg',
            ],
            $html
        );
    }

    /**
     * Get mock db_ops object
     */
    private function get_mock_db_ops() : Symbol_Db_Ops
    {
        $db_ops = $this->createMock( Symbol_Db_Ops::class );
        return $db_ops;
    }

    /**
     * Get mock Mana_Symbol objects
     */
    private function get_mock_mana_symbols() : array
    {
        return [
            $this->get_mock_symbol(),
            $this->get_mock_symbol([ 'plaintext' => '{Q}', 'pattern' => '/\{Q\}/' ]),
        ];
    }

}   // End of class