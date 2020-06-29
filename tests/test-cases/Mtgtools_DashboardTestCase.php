<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Dashboard;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab_Factory;

abstract class Mtgtools_DashboardTestCase extends Mtgtools_UnitTestCase
{
    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        remove_all_filters( 'mtgtools_dashboard_tabs' );
    }
    
    /**
     * Create dashboard module
     */
    protected function create_dashboard( array $params = [] ) : Mtgtools_Dashboard
    {
        $tab_factory = $params['tab_factory'] ?? $this->get_mock_tab_factory();
        $plugin      = $params['plugin'] ?? $this->get_mock_plugin();
        return new Mtgtools_Dashboard( $tab_factory, $plugin );
    }
    
    /**
     * Get tab factory mock
     */
    protected function get_mock_tab_factory() : Dashboard_Tab_Factory
    {
        return $this->createMock( Dashboard_Tab_Factory::class );
    }

}   // End of class