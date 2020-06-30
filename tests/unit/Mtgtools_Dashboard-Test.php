<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Dashboard;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab_Factory;

class Mtgtools_DashboardTest extends Mtgtools_DashboardTestCase
{
    /**
     * Dashboard module
     */
    private $dashboard;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->dashboard = $this->create_dashboard();
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

}   // End of class