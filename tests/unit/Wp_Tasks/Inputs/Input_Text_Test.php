<?php
declare(strict_types=1);

use Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase;
use Mtgtools\Wp_Tasks\Inputs\Input_Text;

class Input_Text_Test extends Mtgtools_UnitTestCase
{
    /**
     * Input test attributes
     */
    const ID = 'test_id';
    const NAME = 'test_name';
    const VALUE = 'test_value';
    const CUSTOM_CLASS = 'test_custom_class';
    const PLACEHOLDER = 'test_placeholder';
    const SIZE = 42;
    const PATTERN = 'test_pattern';

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
                'value' => self::VALUE,
                'placeholder' => self::PLACEHOLDER,
                'size' => self::SIZE,
                'pattern' => self::PATTERN,
            ],
            $html,
            'One or more expected attributes were missing from input element in the markup.'
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
    private function create_input( array $args = [] ) : Input_Text
    {
        $args = array_replace([
            'id' => self::ID,
            'name' => self::NAME,
            'value' => self::VALUE,
            'classes' => [ self::CUSTOM_CLASS ],
            'placeholder' => self::PLACEHOLDER,
            'size' => self::SIZE,
            'pattern' => self::PATTERN,
        ], $args );
        return new Input_Text( $args );
    }

}   // End of class