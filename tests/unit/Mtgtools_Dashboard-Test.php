<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Dashboard;
use Mtgtools\Wp_Task_Library;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab_Factory;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab;

class Mtgtools_DashboardTest extends Mtgtools_UnitTestCase
{
    /**
     * Constants
     */
    const ACTION_NOTICE_PARAMS = [ 'fake_key' => 'A noodly value' ];

    /**
     * Dashboard module
     */
    private $dashboard;

    /**
     * Mock dependencies
     */
    private $tab_factory;
    private $wp_tasks;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->tab_factory = $this->get_mock_tab_factory();
        $this->wp_tasks = $this->createMock( Wp_Task_Library::class );
        $this->dashboard = new Mtgtools_Dashboard( $this->tab_factory, $this->wp_tasks );
        remove_all_filters( 'mtgtools_dashboard_tabs' );
    }

    /**
     * -------------
     *   H O O K S
     * -------------
     */

    /**
     * TEST: Can add hooks
     */
    public function testCanAddHooks() : void
    {
        $result = $this->dashboard->add_hooks();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can enqueue assets
     */
    public function testCanEnqueueAssets() : void
    {
        $result = $this->dashboard->enqueue_assets( 'settings_page_' . MTGTOOLS__ADMIN_SLUG );

        $this->assertNull( $result );
    }
    
    /**
     * TEST: Can create dashboard
     */
    public function testCanCreateDashboard() : void
    {
        $result = $this->dashboard->create_dashboard();

        $this->assertNull( $result );
    }

    /**
     * -----------------------------------
     *   T E M P L A T E   H E L P E R S
     * -----------------------------------
     */
    
    /**
     * TEST: Can print info table
     */
    public function testCanPrintInfoTable() : void
    {
        $rows = [];

        $result = $this->dashboard->print_info_table( $rows );

        $this->assertNull( $result );
    }

    /**
     * TEST: Can print action inputs
     */
    public function testCanPrintActionInputs() : void
    {
        $params = [];

        $result = $this->dashboard->print_action_inputs( $params );

        $this->assertNull( $result );
    }

    /**
     * TEST: Can print action notices
     */
    public function testCanPrintActionNotices() : void
    {
        $actions = [
            'fake_action' => self::ACTION_NOTICE_PARAMS
        ];
        $_GET['action'] = 'fake_action';

        $this->wp_tasks->expects( $this->once() )
            ->method('create_admin_notice')
            ->with( $this->equalTo( self::ACTION_NOTICE_PARAMS ) );
        
        $this->dashboard->print_action_notices( $actions );
    }

    /**
     * ---------------------
     *   D A S H   T A B S
     * ---------------------
     */

    /**
     * TEST: Can get all tabs
     */
    public function testCanGetAllTabs() : void
    {
        $tabs = $this->dashboard->get_tabs();

        $this->assertCount( 1, $tabs );
        $this->assertContainsOnlyInstancesOf( Dashboard_Tab::class, $tabs );
    }

    /**
     * TEST: Can get tab url
     * 
     * @depends testCanGetAllTabs
     */
    public function testCanGetTabUrl() : void
    {
        $url = $this->dashboard->get_tab_url( 'settings' );

        $this->assertIsString( $url );
    }

    /**
     * TEST: Invalid url request throws OutOfRangeException
     * 
     * @depends testCanGetTabUrl
     */
    public function testInvalidUrlRequestThrowsOutOfRangeException() : void
    {
        $this->expectException( \OutOfRangeException::class );

        $this->dashboard->get_tab_url( 'fake-tab' );
    }

    /**
     * -------------------------
     *   A D D I N G   T A B S
     * -------------------------
     */
    
    /**
     * TEST: Can add tab
     * 
     * @depends testCanGetAllTabs
     */
    public function testCanAddTab() : void
    {
        $result = $this->dashboard->add_tab([
            'id' => 'foo_bar',
        ]);
        
        $this->assertNull( $result );
    }

    /**
     * TEST: Adding tab after tab creation throws RuntimeException
     * 
     * @depends testCanAddTab
     */
    public function testAddingTabAtWrongTimeThrowsRuntimeException() : void
    {
        $this->dashboard->get_tabs();

        $this->expectException( \RuntimeException::class );

        $this->dashboard->add_tab([
            'id' => 'foo_bar'
        ]);
    }

    /**
     * -----------------------
     *   A C T I V E   T A B
     * -----------------------
     */
    
    /**
     * TEST: Can get active tab by default
     * 
     * @depends testCanGetAllTabs
     */
    public function testCanGetActiveTabByDefault() : void
    {
        $tab = $this->dashboard->get_active_tab();

        $this->assertInstanceOf( Dashboard_Tab::class, $tab );
    }

    /**
     * TEST: Can get active tab by key
     * 
     * @depends testCanGetActiveTabByDefault
     * @depends testCanAddTab
     */
    public function testCanGetActiveTabByKey() : void
    {
        $this->dashboard->add_tab([
            'id' => 'foo_bar',
        ]);
        $_GET['tab'] = 'foo_bar';

        $tab = $this->dashboard->get_active_tab();

        $this->assertEquals( 'foo_bar', $tab->get_id() );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Get tab factory mock
     */
    protected function get_mock_tab_factory() : Dashboard_Tab_Factory
    {
        $factory = $this->createMock( Dashboard_Tab_Factory::class );
        $factory->method('create_tab')
        ->will( $this->returnCallback( [$this, 'get_mock_dashtab_with_id'] ) );
        return $factory;
    }

    /**
     * Get mock tab with id
     */
    public function get_mock_dashtab_with_id( array $params ) : Dashboard_Tab
    {
        $tab = $this->createMock( Dashboard_Tab::class );
        $tab->method('get_id')->willReturn( $params['id'] );
        return $tab;
    }

}   // End of class