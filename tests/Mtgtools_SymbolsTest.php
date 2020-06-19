<?php
declare(strict_types=1);
use Mtgtools\Mtgtools_Symbols;
use Mtgtools\Symbols\Symbol_Db_Ops;

class Mtgtools_SymbolsTest extends Mtgtools_UnitTestCase
{
    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
    }
    
    /**
     * TEST: Shortcode parser returns string
     */
    public function testParseManaSymbolsReturnsString() : void
    {
        $db_ops = $this->get_mock_db_ops();
        $db_ops->method('get_mana_symbols')->willReturn( $this->get_mock_mana_symbols() );
        $symbols = new Mtgtools_Symbols( $db_ops );

        $result = $symbols->parse_mana_symbols( [], "{T}: Do some biz; {Q}: Do some other biz" );

        $this->assertIsString( $result );
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