<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Updates;
use Mtgtools\Symbols\Symbol_Db_Ops;
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
        $this->db_ops = $this->get_mock_db_ops();
        $this->source = $this->get_mock_mtg_data_source();
        $this->wp_tasks = $this->get_mock_tasks_library();
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
     * TEST: Can print notices
     */
    public function testCanPrintNotices() : void
    {
        $result = $this->updates->print_notices();

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
     * ---------------------
     *   P R O D U C E R S
     * ---------------------
     */

    /**
     * Get mock db_ops object
     */
    private function get_mock_db_ops() : Symbol_Db_Ops
    {
        return $this->createMock( Symbol_Db_Ops::class );
    }

}   // End of class