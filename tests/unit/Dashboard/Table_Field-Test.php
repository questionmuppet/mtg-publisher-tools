<?php
declare(strict_types=1);

use Mtgtools\Dashboard\Table_Field;

class Table_FieldTest extends Mtgtools_UnitTestCase
{
    /**
     * Table field object
     */
    private $field;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->field = new Table_Field([
            'id'    => 'foo_bar',
            'title' => 'Foo Bar'
        ]);
    }

    /**
     * TEST: Can output correct header markup
     */
    public function testCanOutputCorrectHeaderMarkup() : void
    {
        ob_start();
        $this->field->print_header_cell();
        $output = ob_get_clean();

        $this->assertContainsSelector( 'th.mtgtools-table-cell.foo_bar', $output, 'Could not find a <th> element with the appropriate CSS classes in the header markup.' );
        $this->assertElementContains( 'Foo Bar', 'th.mtgtools-table-cell', $output, 'Could not find the title string in the header markup.' );
    }

    /**
     * TEST: Can output correct body markup
     */
    public function testCanOutputCorrectBodyMarkup() : void
    {
        ob_start();
        $this->field->print_body_cell([
            'foo_bar' => '<a href="">Lorem ipsum</a>',
        ]);
        $output = ob_get_clean();

        $this->assertContainsSelector( 'td.mtgtools-table-cell.foo_bar', $output, 'Could not find a <td> element with the appropriate CSS classes in the body markup.' );
        $this->assertElementContains( '<a href="">Lorem ipsum</a>', 'td.mtgtools-table-cell', $output, 'Could not find the row content in the body markup.' );
    }

}   // End of class