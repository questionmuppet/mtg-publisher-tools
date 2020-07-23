<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Options\Option_Number;

class Option_Number_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const MIN = 10;
    const MAX = 20;
    const STEP = 5;

    /**
     * Option object
     */
    private $opt;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->opt = $this->create_option();
    }

    /**
     * TEST: Can sanitize save value
     */
    public function testCanSanitizeSaveValue() : void
    {
        $value = $this->opt->sanitize_save_value( 10 );

        $this->assertIsNumeric( $value );
    }

    /**
     * TEST: Max value enforced
     * 
     * @depends testCanSanitizeSaveValue
     */
    public function testMaxValueEnforcedDuringSanitization() : void
    {
        $value = $this->opt->sanitize_save_value( self::MAX + 10 );

        $this->assertEquals( self::MAX, $value, 'Failed to assert that the maximum value is enforced during sanitization.' );
    }
    
    /**
     * TEST: Min value enforced
     * 
     * @depends testCanSanitizeSaveValue
     */
    public function testMinValueEnforcedDuringSanitization() : void
    {
        $value = $this->opt->sanitize_save_value( self::MIN - 10 );
    
        $this->assertEquals( self::MIN, $value, 'Failed to assert that the minimum value is enforced during sanitization.' );
    }

    /**
     * TEST: Step value enforced
     * 
     * @depends testCanSanitizeSaveValue
     */
    public function testStepIncrementEnforcedDuringSanitization() : void
    {
        $value = $this->opt->sanitize_save_value( 13.14159 );

        $remainder = $value % self::STEP;

        $this->assertEquals( 0, $remainder, 'Failed to assert that the step increment is enforced during sanitization.' );
    }
    
    /**
     * TEST: Can print input
     */
    public function testCanPrintInput() : void
    {
        ob_start();
        $this->opt->print_input();
        $html = ob_get_clean();

        $this->assertIsString( $html );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Create option
     */
    private function create_option( array $args = [] ) : Option_Number
    {
        $args = array_replace([
            'id' => 'fake_option',
            'min' => self::MIN,
            'max' => self::MAX,
            'step' => self::STEP,
        ], $args );
        return new Option_Number( $args );
    }

}   // End of class