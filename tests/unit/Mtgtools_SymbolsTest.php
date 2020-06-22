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
        $symbols = $this->create_symbols_module();

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
        $symbols = $this->create_symbols_module( $db_ops );
        
        $result = $symbols->parse_mana_symbols( [], "{T}: Do some biz; {Q}: Do some other biz" );

        $this->assertIsString( $result );

        return $result;
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
     * TEST: Can install db tables
     */
    public function testCanInstallTables() : void
    {
        $symbols = $this->create_symbols_module();

        $result = $symbols->install_db_tables();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can delete db tables
     */
    public function testCanDeleteTables() : void
    {
        $symbols = $this->create_symbols_module();

        $result = $symbols->delete_db_tables();

        $this->assertNull( $result );
    }

    /**
     * ---------------------
     *   P R O D U C E R S
     * ---------------------
     */

    /**
     * Create symbols module
     */
    private function create_symbols_module( Symbol_Db_Ops $db_ops = null ) : Mtgtools_Symbols
    {
        $db_ops = $db_ops ? $db_ops : $this->get_mock_db_ops();
        $enqueue = $this->get_mock_enqueue();
        return new Mtgtools_Symbols( $db_ops, $enqueue );
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