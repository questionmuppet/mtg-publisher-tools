<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Symbols;
use Mtgtools\Symbols\Symbol_Db_Ops;
use Mtgtools\Mtgtools_Dashboard;
use Mtgtools\Wp_Task_Library;
use Mtgtools\Interfaces\Mtg_Data_Source;

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
    private $wp_tasks;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->db_ops = $this->createMock( Symbol_Db_Ops::class );
        $this->source = $this->createMock( Mtg_Data_Source::class );
        $this->wp_tasks = $this->createMock( Wp_Task_Library::class );
        $this->symbols = new Mtgtools_Symbols( $this->db_ops, $this->source, $this->wp_tasks );
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
     * TEST: Can parse shortcode
     */
    public function testCanParseShortcode() : void
    {
        $symbol = $this->get_mock_symbol([
            'markup' => '<p class="fake-content">A useless paragraph</p>'
        ]);
        $this->db_ops->method('get_mana_symbols')->willReturn( array( $symbol ) );
        
        $html = $this->symbols->parse_mana_symbols( [], "{T}: Do some biz; {Q}: Do some other biz" );

        $this->assertContainsSelector( 'p.fake-content', $html, 'Could not find replacement string in shortcode output.' );
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
    
    /**
     * TEST: Can install db tables
     */
    public function testCanInstallTables() : void
    {
        $result = $this->symbols->install_db_tables();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can delete db tables
     */
    public function testCanDeleteTables() : void
    {
        $result = $this->symbols->delete_db_tables();

        $this->assertNull( $result );
    }

}   // End of class