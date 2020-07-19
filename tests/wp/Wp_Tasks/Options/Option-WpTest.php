<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Options\Option;

/**
 * Class to implement abstract option
 */
class Option_Nonabstract extends Option
{
    public function sanitize( $value ) {
        Option_WPTest::augment_call_count( 'sanitize' );
        return $value;
    }
    public function print_input() : void {}
}

/**
 * Test class
 */
class Option_WPTest extends Mtgtools_UnitTestCase
{
    /**
     * Include call counter
     */
    use FunctionCallCounterTrait;

    /**
     * Constants
     */
    const ID = 'fake_option';
    const FULL_NAME = 'mtgtools_' . self::ID;
    const DEFAULT = 'fake initial value';
    const CUSTOM_CALLBACK_VALUE = 'Match, Poit, & Narf';

    /**
     * Instantiated option
     */
    private $opt;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        self::reset_call_counters([ 'sanitize' ]);
        delete_option( self::FULL_NAME );
        $this->opt = $this->create_option();
    }

    /**
     * ---------------------------
     *   S E T T I N G S   A P I
     * ---------------------------
     */

    /**
     * TEST: Can register setting
     */
    public function testCanRegisterSetting() : void
    {
        $result = $this->opt->wp_register();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can get option id
     */
    public function testCanGetId() : void
    {
        $id = $this->opt->get_id();

        $this->assertEquals( self::ID, $id );
    }

    /**
     * -----------
     *   C R U D
     * -----------
     */

    /**
     * TEST: Can get option value
     */
    public function testCanGetOptionValue() : void
    {
        add_option( self::FULL_NAME, 'Narf!' );

        $value = $this->opt->get_value();

        $this->assertEquals( 'Narf!', $value );
    }
    
    /**
     * TEST: Can delete option
     * 
     * @depends testCanGetOptionValue
     */
    public function testCanDeleteOption() : void
    {
        add_option( self::FULL_NAME, 'Narf!' );

        $this->opt->delete();

        $value = get_option( self::FULL_NAME, null );
        $this->assertNull( $value );
    }
    
    /**
     * TEST: Can update option
     * 
     * @depends testCanGetOptionValue
     */
    public function testCanUpdateOption() : void
    {
        add_option( self::FULL_NAME, 'Narf!' );

        $this->opt->update( 'Zort!' );
        
        $value = $this->opt->get_value();
        $this->assertEquals( 'Zort!', $value );
    }

    /**
     * TEST: Adding option saves default value
     * 
     * @depends testCanUpdateOption
     */
    public function testAddingOptionSavesDefaultValue() : void
    {
        $this->opt->add_to_db();
        
        $value = $this->opt->get_value();
        $this->assertEquals( self::DEFAULT, $value );
    }

    /**
     * TEST: Updating passes through sanitization callback
     * 
     * @depends testCanUpdateOption
     */
    public function testSanitizationCallbackFiltersUpdate() : void
    {
        $this->opt->update( 'Troz!' );

        $count = self::get_call_count('sanitize');

        $this->assertEquals( 1, $count );
    }

    /**
     * TEST: Can provide external sanitization callback
     * 
     * @depends testCanUpdateOption
     */
    public function testCanProvideExternalSanitizationCallback() : void
    {
        $opt = $this->create_option([ 'sanitization' => function( $value ) {
            return self::CUSTOM_CALLBACK_VALUE;
        }]);

        $opt->update( 'Narf!' );

        $this->assertEquals( self::CUSTOM_CALLBACK_VALUE, $opt->get_value() );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Create option
     */
    private function create_option( array $args = [] ) : Option_Nonabstract
    {
        $args = array_replace([
            'id' => self::ID,
            'page' => 'fake_options_page',
            'default_value' => self::DEFAULT,
            'label' => 'fake_label',
            'section' => 'fake_section',
        ], $args );
        return new Option_Nonabstract( $args );
    }

}   // End of class