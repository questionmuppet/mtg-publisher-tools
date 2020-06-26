<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Dashboard;
use Mtgtools\Dashboard\Dashboard_Tab;

class Mtgtools_Dashboard_WPTest extends Mtgtools_UnitTestCase
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
        $_GET['tab'] = 'settings';
    }
    
    /**
     * ---------------------------
     *   W P   C A L L B A C K S
     * ---------------------------
     */

    /**
     * TEST: Can create dashboard
     */
    public function testCanCreateDashboard() : void
    {
        $result = $this->dashboard->create_dashboard();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can display dashboard
     */
    public function testCanDisplayDashboard() : string
    {
        wp_get_current_user()->add_cap( 'manage_options' );

        ob_start();
        $this->dashboard->display_dashboard();
        $html = ob_get_clean();

        $this->assertIsString( $html );

        return $html;
    }

    /**
     * TEST: Dashboard output contains correct markup
     * 
     * @depends testCanDisplayDashboard
     */
    public function testDashboardOutputContainsCorrectMarkup( string $html ) : void
    {
        $this->assertContainsSelector( 'div.wrap', $html );
    }

    /**
     * TEST: Can include data table
     * 
     * @depends testCanDisplayDashboard
     */
    public function testCanIncludeDataTable() : void
    {
        ob_start();
        $this->dashboard->include_data_table();
        $html = ob_get_clean();

        $this->assertIsString( $html );
    }
    
    /**
     * TEST: Can enqueue assets
     */
    public function testCanEnqueueAssets() : void
    {
        $result = $this->dashboard->enqueue_assets( 'settings_page_' . MTGTOOLS__ADMIN_SLUG );

        $this->assertNull( $result );
    }

}   // End of class