<?php
declare(strict_types=1);

use Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase;
use Mtgtools\Wp_Tasks\Inputs\Input_Checkbox;

class Input_Checkbox_Test extends Mtgtools_UnitTestCase
{
    /**
     * Input test attributes
     */
    const ID = 'test_id';
    const NAME = 'test_name';
    const CUSTOM_CLASS = 'test_custom_class';
    const LABEL = 'Fake checkbox label';

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
        $selector = implode( '.', ['input', self::CSS_CLASS, self::CUSTOM_CLASS] );
        $this->assertContainsSelector(
            $selector,
            $html,
            'Failed to find an input element with the expected class in the markup.'
        );
        $this->assertHasElementWithAttributes(
            [
                'id' => self::ID,
                'name' => self::NAME,
                'value' => 1,
            ],
            $html,
            'One or more expected attributes were missing from input element in the markup.'
        );
        $this->assertStringContainsString( self::LABEL, $html, 'Failed to find the expected label text in the markup.' );
    }

    /**
     * TEST: Checkbox is marked checked when value is true
     * 
     * @depends testInputContainsCorrectAttributes
     */
    public function testCheckboxIsCheckedWhenValueIsTrue() : void
    {
        $input = $this->create_input([ 'value' => true ]);

        $html = $input->get_markup();

        $this->assertHasElementWithAttributes(
            [
                'id' => self::ID,
                'checked' => 'checked',
            ],
            $html,
            'Did not find the "checked" attribute set to true for a checkbox with a value of "true".'
        );
    }

    /**
     * TEST: Checkbox is unchecked when value is false
     * 
     * @depends testCheckboxIsCheckedWhenValueIsTrue
     */
    public function testCheckboxIsUncheckedWhenValueIsFalse() : void
    {
        $input = $this->create_input([ 'value' => false ]);

        $html = $input->get_markup();

        $this->assertNotHasElementWithAttributes(
            [
                'id' => self::ID,
                'checked' => 'checked',
            ],
            $html,
            'Found the "checked" attribute set to true for a checkbox with a value of "false".'
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
    private function create_input( array $args = [] ) : Input_Checkbox
    {
        $args = array_replace([
            'id' => self::ID,
            'name' => self::NAME,
            'classes' => [ self::CUSTOM_CLASS ],
            'label' => self::LABEL,
        ], $args );
        return new Input_Checkbox( $args );
    }

}   // End of class