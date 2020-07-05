<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Updates;
use Mtgtools\Symbols\Symbol_Db_Ops;
use Mtgtools\Interfaces\Mtg_Data_Source;
use Mtgtools\Wp_Task_Library;
use Mtgtools\Mtgtools_Dashboard;

class Mtgtools_Updates_Test extends Mtgtools_UnitTestCase
{
    /**
     * Updates module instance
     */
    private $updates;

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
        $this->updates = new Mtgtools_Updates( $this->db_ops, $this->source, $this->wp_tasks );
    }

    /**
     * -------------
     *   T E S T S
     * -------------
     */

    /**
     * TEST: Can add hooks
     */
    public function testCanAddHooks() : void
    {
        $result = $this->updates->add_hooks();

        $this->assertNull( $result );
    }
    
    /**
     * TEST: Can add dashboard tab
     */
    public function testCanAddDashTab() : void
    {
        $dashboard = $this->createMock( Mtgtools_Dashboard::class );
        
        $result = $this->updates->add_dash_tab( $dashboard );
        
        $this->assertNull( $result );
    }

    /**
     * TEST: Can get status info
     */
    public function testCanGetStatusInfo() : void
    {
        $info = $this->updates->get_status_info();

        $this->assertIsArray( $info );
    }

    /**
     * TEST: Can print notices
     */
    public function testCanPrintNotices() : void
    {
        $result = $this->updates->print_notices();

        $this->assertNull( $result );
    }

    /**
     * -----------------
     *   U P D A T E S
     * -----------------
     */

    /**
     * TEST: Can update symbols
     */
    public function testCanUpdateSymbols() : void
    {
        $this->source->method('get_mana_symbols')->willReturn( $this->get_mock_symbols(2) );
        $this->db_ops->method('add_symbol')->willReturn( true );

        $result = $this->updates->update_symbols();

        $this->assertIsArray( $result );
        $this->assertEquals( 'updated', $result['action'], 'Failed to assert that the "updated" action is passed back to the admin-post handler on success.' );
    }

    /**
     * TEST: Updating symbols returns correct action on failure
     * 
     * @depends testCanUpdateSymbols
     */
    public function testUpdatingReturnsCorrectActionOnFailure() : void
    {
        $this->source->method('get_mana_symbols')->willReturn( $this->get_mock_symbols(2) );
        $this->db_ops->method('add_symbol')->willReturn( false );

        $result = $this->updates->update_symbols();

        $this->assertEquals( 'checked_current', $result['action'], 'Failed to assert that the "checked_current" action is passed back to the admin-post handler on failure.' );
    }

}   // End of class