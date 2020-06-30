<?php
declare(strict_types=1);

use Mtgtools\Dashboard\Tabs\Dashboard_Tab;
use Mtgtools\Tasks\Enqueue\Asset;
use Mtgtools\Tasks\Tables\Table_Data;

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
     * ---------------
     *   A S S E T S
     * ---------------
     */

    /**
     * TEST: Cannot be constructed with invalid assets
     */
    public function testCannotBeConstructedWithInvalidAssets() : void
    {
        $this->expectException( \InvalidArgumentException::class );

        $tab = $this->create_tab([
            'assets' => [ 'I am not an Asset object' ]
        ]);
    }

    /**
     * TEST: Can get assets
     */
    public function testCanGetAssets() : void
    {
        $asset = $this->createMock( Asset::class );
        $tab = $this->create_tab([
            'assets' => [ $asset ]
        ]);

        $assets = $tab->get_assets();

        $this->assertContainsOnlyInstancesOf( Asset::class, $assets );
    }

    /**
     * ---------------
     *   T A B L E S
     * ---------------
     */

    /**
     * TEST: Cannot be constructed with invalid table data
     */
    public function testCannotBeConstructedWithInvalidTableData() : void
    {
        $this->expectException( \InvalidArgumentException::class );

        $tab = $this->create_tab([
            'tables' => [ 'I am not a Table_Data object' ]
        ]);
    }

    /**
     * TEST: Can get table data
     */
    public function testCanGetTableData() : void
    {
        $table = $this->createMock( Table_Data::class );
        $tab = $this->create_tab([
            'tables' => [
                'baz' => $table
            ]
        ]);

        $result = $tab->get_table_data( 'baz' );

        $this->assertInstanceOf( Table_Data::class, $result );
    }

    /**
     * TEST: Invalid table data request throws OutOfRangeException
     * 
     * @depends testCanGetTableData
     */
    public function testInvalidTableDataRequestThrowsOutOfRangeException() : void
    {
        $tab = $this->create_tab();

        $this->expectException( \OutOfRangeException::class );

        $result = $tab->get_table_data( 'invalid_table' );
    }

    /**
     * -------------------------
     *   H T M L   O U T P U T
     * -------------------------
     */

    /**
     * TEST: Can get href attribute
     */
    public function testCanGetHref() : void
    {
        $tab = $this->create_tab();
        
        $href = $tab->get_href();
        
        $pattern = sprintf( '/page=%s&tab=foo_bar$/', MTGTOOLS__ADMIN_SLUG );
        $this->assertRegExp( $pattern, $href );
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
        ], $args );
        return new Dashboard_Tab( $args );
    }

}   // End of class