<?php
declare(strict_types=1);

use Mtgtools\Dashboard\Table_Data;
use Mtgtools\Dashboard\Table_Field;

class Table_DataTest extends Mtgtools_UnitTestCase
{
    /**
     * Table data object
     */
    private $data;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->data = $this->create_data();
    }

    /**
     * -------------
     *   T E S T S
     * -------------
     */

    /**
     * TEST: Can get identifier key
     */
    public function testCanGetKey() : void
    {
        $key = $this->data->get_key();

        $this->assertEquals( 'fake_table', $key );
    }

    /**
     * TEST: Can get table fields
     */
    public function testCanGetTableFields() : void
    {
        $fields = $this->data->get_fields();

        $this->assertContainsOnlyInstancesOf( Table_Field::class, $fields );
    }
    
    /**
     * TEST: Can get data rows
     */
    public function testCanGetDataRows() : void
    {
        $rows = $this->data->get_rows();

        $this->assertIsArray( $rows );
        $this->assertCount( 1, $rows );
    }

    /**
     * TEST: Can pass filter to callback
     * 
     * @depends testCanGetDataRows
     */
    public function testCanPassFilterToCallback() : void
    {
        $this->data->set_filter( 'alternate' );

        $rows = $this->data->get_rows();

        $this->assertCount( 2, $rows );
    }

    /**
     * ---------------------
     *   P R O D U C E R S
     * ---------------------
     */

    /**
     * Create Table_Data object
     */
    private function create_data( array $params = [] ) : Table_Data
    {
        $params = array_merge([
            'id'           => 'fake_table',
            'fields'       => [
                'foo' => array(),
                'bar' => array(),
            ],
            'row_callback' => $this->get_callback(),
        ], $params );
        return new Table_Data( $params );
    }

    /**
     * Get row callback
     */
    private function get_callback() : callable
    {
        return function( string $filter = '' ) {
            return 'alternate' === $filter
                ? [ 'Element one', 'Element two' ]
                : [ 'A single member' ];
        };
    }

}   // End of class