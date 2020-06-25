<?php
declare(strict_types=1);

use Mtgtools\Dashboard\Table_Data;
use Mtgtools\Dashboard\Table_Field;

class Table_DataTest extends Mtgtools_UnitTestCase
{
    /**
     * TEST: Non array row data in constructor throws InvalidArgumentException
     */
    public function testNonArrayRowDataThrowsInvalidArgumentException() : void
    {
        $rows = [
            array( 'foo' => 'An array, properly formed' ),
            "An errant string",
        ];
        
        $this->expectException( \InvalidArgumentException::class );

        $this->create_data([ 'row_data' => $rows ]);
    }

    /**
     * TEST: Can get table fields
     */
    public function testCanGetTableFields() : void
    {
        $data = $this->create_data();

        $fields = $data->get_fields();

        $this->assertContainsOnlyInstancesOf( Table_Field::class, $fields );
    }
    
    /**
     * TEST: Can get data rows
     */
    public function testCanGetDataRows() : void
    {
        $data = $this->create_data();

        $rows = $data->get_rows();

        $this->assertIsArray( $rows );
    }

    /**
     * Create Table_Data object
     */
    private function create_data( array $params = [] ) : Table_Data
    {
        $params = array_merge([
            'fields'   => [
                'foo' => array(),
                'bar' => array(),
            ],
            'row_data' => [
                array(
                    'foo' => 'Test row 1',
                    'bar' => 'Isn\'t it nifty',
                ),
                array(
                    'foo' => 'Test row 2',
                    'bar' => 'I like this one too',
                ),
            ]
        ], $params );
        return new Table_Data( $params );
    }

}   // End of class