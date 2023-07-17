<?php
declare(strict_types=1);

use Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase;
use Mtgtools\Wp_Tasks\Inputs\Input_Select;

class Input_Select_Test extends Mtgtools_UnitTestCase
{
    /**
     * Input test attributes
     */
    const ID = 'test_id';
    const NAME = 'test_name';
    const VALUE = 'test_value_2';
    const CUSTOM_CLASS = 'test_custom_class';
    const OPTIONS = [
        'test_value_1' => 'narf',
        'test_value_2' => 'zort',
    ];

    /**
     * Expected CSS class string
     */
    const CSS_CLASS = MTGTOOLS__ADMIN_SLUG . '-dashboard-input';

    /**
     * TEST: Can print input
     */
    public function testCanPrintInput() : void
    {
        $input = $this->create_input();

        ob_start();
        $input->print();
        $html = ob_get_clean();

        $this->assertIsString( $html );
    }

    /**
     * TEST: Can get markup
     * 
     * @depends testCanPrintInput
     */
    public function testCanGetMarkup() : string
    {
        $input = $this->create_input();

        $html = $input->get_markup();

        $this->assertIsString( $html );

        return $html;
    }

    /**
     * TEST: Input element contains correct attributes
     * 
     * @depends testCanGetMarkup
     */
    public function testInputContainsCorrectAttributes( string $html ) : void
    {
        $selector = implode( '.', ['select', self::CSS_CLASS, self::CUSTOM_CLASS] );
        $this->assertContainsSelector(
            $selector,
            $html,
            'Failed to find a <select> element with the expected class in the markup.'
        );
        $this->assertHasElementWithAttributes(
            [
                'id' => self::ID,
                'name' => self::NAME,
                'value' => self::VALUE,
            ],
            $html,
            'One or more expected attributes were missing from <select> element in the markup.'
        );
    }

    /**
     * TEST: Select contains option elements
     * 
     * @depends testInputContainsCorrectAttributes
     */
    public function testSelectContainsOptionElements() : string
    {
        $input = $this->create_input();

        $html = $input->get_markup();

        $this->assertSelectorCount(
            2,
            sprintf( "select.%s option", self::CSS_CLASS ),
            $html,
            'Failed to find the expected number of <option> elements in the markup.'
        );

        return $html;
    }

    /**
     * TEST: Correct option is marked selected
     * 
     * @depends testSelectContainsOptionElements
     */
    public function testCorrectOptionIsMarkedSelected( string $html ) : void
    {
        $this->assertHasElementWithAttributes(
            [
                'value' => self::VALUE,
                'selected' => 'selected'
            ],
            $html,
            'Failed to find a <option> element with the selected attribute matching the <select> value.'
        );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Create input
     */
    private function create_input( array $args = [] ) : Input_Select
    {
        $args = array_replace([
            'id' => self::ID,
            'name' => self::NAME,
            'value' => self::VALUE,
            'classes' => [ self::CUSTOM_CLASS ],
            'options' => self::OPTIONS,
        ], $args );
        return new Input_Select( $args );
    }

}   // End of class