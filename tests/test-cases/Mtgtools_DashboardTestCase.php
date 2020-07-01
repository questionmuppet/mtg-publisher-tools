<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Dashboard;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab_Factory;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab;

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
        $wp_tasks    = $params['wp_tasks'] ?? $this->get_mock_tasks_library();
        return new Mtgtools_Dashboard( $tab_factory, $wp_tasks );
    }
    
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