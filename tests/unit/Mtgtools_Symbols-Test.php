<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Symbols;
use Mtgtools\Symbols\Symbol_Db_Ops;
use Mtgtools\Mtgtools_Dashboard;

class Mtgtools_Symbols_Test extends Mtgtools_UnitTestCase
{
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
        $symbols = $this->create_symbols_module();

        $result = $symbols->add_hooks();

        $this->assertNull( $result );
    }

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
     * TEST: Can parse shortcode
     */
    public function testCanParseShortcode() : void
    {
        $symbol = $this->get_mock_symbol([
            'markup' => '<p class="fake-content">A useless paragraph</p>'
        ]);
        $db_ops = $this->get_mock_db_ops();
        $db_ops->method('get_mana_symbols')->willReturn( array( $symbol ) );
        $symbols = $this->create_symbols_module([ 'db_ops' => $db_ops, ]);
        
        $html = $symbols->parse_mana_symbols( [], "{T}: Do some biz; {Q}: Do some other biz" );

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
        $symbols = $this->create_symbols_module();
        $dashboard = $this->createMock( Mtgtools_Dashboard::class );

        $result = $symbols->add_dash_tab( $dashboard );

        $this->assertNull( $result );
    }

    /**
     * TEST: Can get symbol list data for tables
     */
    public function testCanGetSymbolListData() : void
    {
        $db_ops = $this->get_mock_db_ops();
        $db_ops->method('get_mana_symbols')->willReturn( $this->get_mock_symbols(2) );
        $symbols = $this->create_symbols_module([ 'db_ops' => $db_ops ]);

        $rows = $symbols->get_symbol_list_data();

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
        $db_ops = $this->get_mock_db_ops();
        $db_ops->expects( $this->once() )
            ->method( 'get_mana_symbols' )
            ->with( $this->arrayHasKey( 'plaintext' ) );

        $symbols = $this->create_symbols_module([ 'db_ops' => $db_ops ]);

        $symbols->get_symbol_list_data( 'A nice filter' );
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
        $source = $this->get_mock_mtg_data_source();
        $source->method('get_mana_symbols')->willReturn( $this->get_mock_symbols(2) );
        $symbols = $this->create_symbols_module([ 'source' => $source ]);

        $result = $symbols->import_symbols();

        $this->assertNull( $result );
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
    private function create_symbols_module( array $args = [] ) : Mtgtools_Symbols
    {
        $db_ops  = $args['db_ops'] ?? $this->get_mock_db_ops();
        $source  = $args['source'] ?? $this->get_mock_mtg_data_source();
        $plugin  = $args['plugin'] ?? $this->get_mock_plugin();
        return new Mtgtools_Symbols( $db_ops, $source, $plugin );
    }

    /**
     * Get mock db_ops object
     */
    private function get_mock_db_ops() : Symbol_Db_Ops
    {
        $db_ops = $this->createMock( Symbol_Db_Ops::class );
        return $db_ops;
    }

}   // End of class