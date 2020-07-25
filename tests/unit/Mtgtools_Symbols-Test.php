<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Symbols;
use Mtgtools\Db\Services\Symbol_Db_Ops;
use Mtgtools\Mtgtools_Dashboard;
use Mtgtools\Mtgtools_Plugin;
use Mtgtools\Sources\Mtg_Data_Source;
use Mtgtools\Exceptions\Db\NoResultsException;

class Mtgtools_Symbols_Test extends Mtgtools_UnitTestCase
{
    /**
     * Symbols module
     */
    private $symbols;

    /**
     * Mock dependencies
     */
    private $db_ops;
    private $source;
    private $plugin;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->db_ops = $this->createMock( Symbol_Db_Ops::class );
        $this->source = $this->createMock( Mtg_Data_Source::class );
        $this->plugin = $this->createMock( Mtgtools_Plugin::class );
        $this->symbols = new Mtgtools_Symbols( $this->db_ops, $this->source, $this->plugin );
    }

    /**
     * -------------------
     *   W P   H O O K S
     * -------------------
     */

    /**
     * TEST: Can add hooks
     */
    public function testCanAddHooks() : void
    {
        $result = $this->symbols->add_hooks();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can enqueue assets
     */
    public function testCanEnqueueAssets() : void
    {
        $result = $this->symbols->enqueue_assets();

        $this->assertNull( $result );
    }
    
    /**
     * TEST: Can generate a single symbol
     */
    public function testCanGenerateSingleSymbol() : void
    {
        $this->db_ops
            ->method('get_symbol_by_plaintext')
            ->with( $this->equalTo('{U}') )
            ->willReturn( $this->get_mock_symbol() );
        
        $html = $this->symbols->insert_single_symbol([
            'key' => '{U}'
        ]);

        $this->assertIsString( $html );
    }

    /**
     * TEST: Inserting invalid symbol key returns content
     * 
     * @depends testCanGenerateSingleSymbol
     */
    public function testInsertingInvalidSymbolKeyReturnsContent() : void
    {
        $this->db_ops
            ->method('get_symbol_by_plaintext')
            ->with( $this->equalTo('invalid_code') )
            ->willThrowException( new NoResultsException( "Invalid code. No symbol found." ) );
        
        $content = 'A nice string.';
        $html = $this->symbols->insert_single_symbol([
            'key' => 'invalid_code'
        ], $content );

        $this->assertEquals( $content, $html );
    }

    /**
     * TEST: Can parse shortcode
     */
    public function testCanParseShortcode() : void
    {
        $symbol = $this->get_mock_symbol([
            'markup' => '<p class="fake-content">A useless paragraph</p>'
        ]);
        $this->db_ops->method('get_mana_symbols')->willReturn( array( $symbol ) );
        
        $html = $this->symbols->parse_oracle_text( [], "{T}: Do some biz; {Q}: Do some other biz" );

        $this->assertContainsSelector( 'p.fake-content', $html, 'Could not find replacement string in shortcode output.' );
    }

    /**
     * TEST: Reminder text in oracle text is wrapped
     * 
     * @depends testCanParseShortcode
     */
    public function testReminderTextInOracleTextIsWrapped() : void
    {
        $reminder = "(Each time you attack alone pump a creature's biz.)";
        $html = $this->symbols->parse_oracle_text( [], "Exalted $reminder" );

        $this->assertContainsSelector( 'span.mtg-reminder-text', $html, 'Failed to assert that the reminder text <span> tag was inserted.' );
        $this->assertElementContains( $reminder, 'span.mtg-reminder-text', $html, 'Failed to assert that the reminder text was contained in the <span> tags.' );
    }

    /**
     * -----------------------------
     *   D A S H B O A R D   T A B
     * -----------------------------
     */

    /**
     * TEST: Can add dash tab
     */
    public function testCanAddDashTab() : void
    {
        $dashboard = $this->createMock( Mtgtools_Dashboard::class );

        $result = $this->symbols->add_dash_tab( $dashboard );

        $this->assertNull( $result );
    }

    /**
     * TEST: Can get symbol list data for tables
     */
    public function testCanGetSymbolListData() : void
    {
        $this->db_ops->method('get_mana_symbols')->willReturn( $this->get_mock_symbols(2) );

        $rows = $this->symbols->get_symbol_list_data();

        $this->assertCount( 2, $rows );
        $this->assertContainsOnly( 'array', $rows );
    }

    /**
     * TEST: Can pass filter to Db_Ops for list data
     * 
     * @depends testCanGetSymbolListData
     */
    public function testGetSymbolListDataCanPassFilter() : void
    {
        $this->db_ops->expects( $this->once() )
            ->method( 'get_mana_symbols' )
            ->with( $this->arrayHasKey( 'plaintext' ) );
        
        $this->symbols->get_symbol_list_data( 'A nice filter' );
    }

    /**
     * ---------------------------
     *   I N S T A L L A T I O N
     * ---------------------------
     */

    /**
     * TEST: Can import symbols
     */
    public function testCanImportSymbols() : void
    {
        $this->source->method('get_mana_symbols')->willReturn( $this->get_mock_symbols(2) );

        $result = $this->symbols->import_symbols();

        $this->assertNull( $result );
    }

}   // End of class