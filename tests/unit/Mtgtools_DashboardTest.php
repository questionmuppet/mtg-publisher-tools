<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Dashboard;
use Mtgtools\Dashboard\Dashboard_Tab;

class Mtgtools_DashboardTest extends Mtgtools_UnitTestCase
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
        $this->dashboard = new Mtgtools_Dashboard( $this->get_mock_plugin() );
        remove_all_filters( 'mtgtools_dashboard_tab_definitions' );
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
    public function testInvalidUrlReqestThrowsOutOfRangeException() : void
    {
        $this->expectException( \OutOfRangeException::class );

        $this->dashboard->get_tab_url( 'fake-tab' );
    }

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
     * @depends testCanGetAllTabs
     */
    public function testCanGetActiveTabByKey() : void
    {
        add_filter( 'mtgtools_dashboard_tab_definitions', function( $defs ) {
            $defs['foo_bar'] = [];
            return $defs;
        });
        $_GET['tab'] = 'foo_bar';

        $tab = $this->dashboard->get_active_tab();

        $this->assertEquals( 'foo_bar', $tab->get_id() );
    }

}   // End of class