<?php
declare(strict_types=1);

use Mtgtools\Dashboard\Dashboard_Tab;
use Mtgtools\Mtgtools_Enqueue;
use Mtgtools\Dashboard\Table_Data;

class Dashboard_Tab_Test extends Mtgtools_UnitTestCase
{
    /**
     * TEST: Can get id
     */
    public function testCanGetId() : void
    {
        $tab = $this->create_tab();

        $id = $tab->get_id();

        $this->assertEquals( 'foo_bar', $id );
    }

    /**
     * TEST: Can enqueue assets
     */
    public function testCanEnqueueAssets() : void
    {
        $tab = $this->create_tab();
        $plugin = $this->get_mock_plugin();

        $result = $tab->enqueue_assets( $plugin );

        $this->assertNull( $result );
    }

    /**
     * TEST: Can get table data
     */
    public function testCanGetTableData() : void
    {
        $rows = [
            array(),
            array(),
        ];
        $tab = $this->create_tab([ 'row_data' => $rows ]);
        
        $data = $tab->get_table_data();

        $this->assertInstanceOf( Table_Data::class, $data );
    }

    /**
     * TEST: Can get table data via callback
     * 
     * @depends testCanGetTableData
     */
    public function testCanGetTableDataViaCallback() : void
    {
        $tab = $this->create_tab([ 'row_data_callback' => function() {
            return [
                array(),
                array(),
            ];
        }]);

        $data = $tab->get_table_data();

        $this->assertInstanceOf( Table_Data::class, $data );
    }

    /**
     * TEST: Can get href attribute
     */
    public function testCanGetHref() : string
    {
        $tab = $this->create_tab();
        
        $href = $tab->get_href();
        
        $pattern = sprintf( '/page=%s&tab=foo_bar$/', MTGTOOLS__ADMIN_SLUG );
        $this->assertRegExp( $pattern, $href );
        
        return $href;
    }

    /**
     * TEST: Inactive tab outputs correct markup
     */
    public function testInactiveTabOutputsCorrectMarkup() : void
    {
        $tab = $this->create_tab();
        
        ob_start();
        $tab->output_nav_tab( 'fake' );
        $html = ob_get_clean();

        $this->assertContainsSelector( 'a.nav-tab', $html );
        $this->assertNotContainsSelector( 'a.nav-tab-active', $html );
    }
    
    /**
     * TEST: Active tab outputs correct markup
     */
    public function testActiveTabOutputsCorrectMarkup() : void
    {
        $tab = $this->create_tab();
        
        ob_start();
        $tab->output_nav_tab( 'foo_bar' );
        $html = ob_get_clean();

        $this->assertContainsSelector( 'a.nav-tab.nav-tab-active', $html );
    }

    /**
     * ---------------------
     *   P R O D U C E R S
     * ---------------------
     */

    /**
     * Create Dashboard_Tab object
     */
    private function create_tab( array $args = [] ) : Dashboard_Tab
    {
        $args = array_merge([
            'id'      => 'foo_bar',
            'title'   => 'Foo Bar',
            'scripts' => [
                [
                    'key'  => 'fake_script',
                    'path' => 'path/to/fake/script.js',
                ],
            ],
            'styles'  => [
                [
                    'key'  => 'fake_style',
                    'path' => 'path/to/fake/style.css',
                ],
            ],
        ], $args );
        return new Dashboard_Tab( $args );
    }

}   // End of class