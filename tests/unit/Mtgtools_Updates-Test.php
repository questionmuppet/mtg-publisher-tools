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
     * TEST: Can print notices
     */
    public function testCanPrintNotices() : void
    {
        $result = $this->updates->print_notices();

        $this->assertNull( $result );
    }

}   // End of class