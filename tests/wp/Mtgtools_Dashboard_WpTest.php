<?php
declare(strict_types=1);

use Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase;
use Mtgtools\Mtgtools_Dashboard;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab_Factory;
use Mtgtools\Wp_Tasks\Tables\Table_Data;
use Mtgtools\Exceptions\Admin_Post\ParameterException;

class Mtgtools_Dashboard_WpTest extends Mtgtools_UnitTestCase
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
     * TEST: Can display dashboard
     */
    public function testCanDisplayDashboard() : string
    {
        $dashboard = $this->create_live_dashboard();
        wp_get_current_user()->add_cap( 'manage_options' );

        ob_start();
        $dashboard->display_dashboard();
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
     * TEST: Can display data table
     * 
     * @depends testCanDisplayDashboard
     */
    public function testCanDisplayDataTable() : string
    {
        $dashboard = $this->create_live_dashboard();
        $dashboard->add_tab( $this->get_test_tab_params() );
        $_GET['tab'] = 'foo_bar';

        ob_start();
        $dashboard->display_table( 'foo_table' );
        $html = ob_get_clean();

        $this->assertIsString( $html );

        return $html;
    }

    /**
     * TEST: Data table contains correct markup
     * 
     * @depends testCanDisplayDataTable
     */
    public function testDataTableContainsCorrectMarkup( string $html ) : void
    {
        $this->assertContainsSelector( 'div.mtgtools-table-wrapper', $html, 'Failed to find the expected CSS class "mtgtools-table-wrapper" in the data table markup.' );
    }

    /**
     * TEST: Can send Ajax table update
     * 
     * @depends testDataTableContainsCorrectMarkup
     */
    public function testCanSendAjaxTableUpdate() : string
    {
        $dashboard = $this->create_live_dashboard();
        $dashboard->add_tab( $this->get_test_tab_params() );
        
        $ajax_data = $dashboard->update_data_table([
            'tab'    => 'foo_bar',
            'table'  => 'foo_table',
            'filter' => '',
        ]);

        $markup = $ajax_data['transients']['tableBody'];
        $this->assertIsString( $markup );
        
        return $markup;
    }

    /**
     * TEST: Table update returns correct markup
     * 
     * @depends testCanSendAjaxTableUpdate
     */
    public function testAjaxTableUpdateReturnsCorrectMarkup( string $html ) : void
    {
        $message = 'Failed to find the expected number of <td> cells, with the expected CSS class, in the table body markup.';
        $this->assertSelectorCount( 2, 'td.mtgtools-table-cell.character', $html, $message );
        $this->assertSelectorCount( 2, 'td.mtgtools-table-cell.quote', $html, $message );
    }

    /**
     * TEST: Table update with invalid tab throws ParameterException
     * 
     * @depends testCanSendAjaxTableUpdate
     */
    public function testTableUpdateWithInvalidTabThrowsParameterException() : void
    {
        $dashboard = $this->create_live_dashboard();
        $dashboard->add_tab( $this->get_test_tab_params() );
        
        $this->expectException( ParameterException::class );

        $dashboard->update_data_table([
            'tab'    => 'undefined_bad_tab',
            'table'  => 'foo_table',
            'filter' => '',
        ]);
    }

    /**
     * TEST: Table update with invalid table key throws ParameterException
     * 
     * @depends testCanSendAjaxTableUpdate
     */
    public function testTableUpdateWithInvalidTableKeyThrowsParameterException() : void
    {
        $dashboard = $this->create_live_dashboard();
        $dashboard->add_tab( $this->get_test_tab_params() );
        
        $this->expectException( ParameterException::class );

        $dashboard->update_data_table([
            'tab'    => 'foo_bar',
            'table'  => 'undefined_table',
            'filter' => '',
        ]);
    }

    /**
     * ---------------------
     *   P R O D U C E R S
     * ---------------------
     */

    /**
     * Create dashboard with live dependencies
     */
    private function create_live_dashboard() : Mtgtools_Dashboard
    {
        $factory = new Dashboard_Tab_Factory();
        $plugin = Mtgtools\Mtgtools_Plugin::get_instance();
        return new Mtgtools_Dashboard( $factory, $plugin );
    }

    /**
     * Get params for test tab
     */
    private function get_test_tab_params() : array
    {
        return array(
            'id'     => 'foo_bar',
            'tables' => [
                'foo_table' => new Table_Data([
                    'id'           => 'foo_table',
                    'fields'       => array(
                        'character' => [],
                        'quote'     => [],
                    ),
                    'row_callback' => function( $filter ) {
                        return array(
                            [
                                'character' => 'Pinky',
                                'quote'     => 'Narf',
                            ],
                            [
                                'character' => 'Brain',
                                'quote'     => 'The same thing we do every night'
                            ],
                        );
                    },
                ])
            ],
        );
    }

}   // End of class