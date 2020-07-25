<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Options\Plugin_Option;

/**
 * Class to implement abstract option
 */
class Option_Nonabstract extends Plugin_Option
{
    protected function sanitize( $value ) {
        Plugin_Option_WPTest::augment_call_count( 'sanitize' );
        return $value;
    }
    public function print_input() : void {}
}

/**
 * Test class
 */
class Plugin_Option_WPTest extends Mtgtools_UnitTestCase
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
    const LABEL = 'fake label';
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
     * TEST: Can get public properties
     */
    public function testCanGetPublicProps() : void
    {
        $this->assertEquals( self::ID, $this->opt->get_id(), "Failed to retreieve public property 'id'." );
        $this->assertEquals( self::LABEL, $this->opt->get_label(), "Failed to retreieve public property 'label'." );
        $this->assertEquals( self::FULL_NAME, $this->opt->get_option_name(), "Failed to assert that public property 'option_name' contained the expected prefix." );
    }

    /**
     * TEST: Can whitelist for WP settings pages
     */
    public function testCanRegisterWithWp() : void
    {
        $result = $this->opt->wp_register( 'fake_page' );

        $this->assertNull( $result );
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
     * TEST: Save-option action hook passes correct arguments
     * 
     * @depends testCanUpdateOption
     */
    public function testSaveOptionActionHookPassesCorrectArguments() : void
    {
        $callback = $this->createPartialMock( stdClass::class, ['__invoke'] );
        $callback
            ->expects( $this->once() )
            ->method('__invoke')
            ->with(
                'Narf!',
                self::DEFAULT,
                $this->isInstanceOf( Plugin_Option::class )
            );
        
        add_action( 'mtgtools_save_option_' . self::ID, $callback, 10, 3 );

        $this->opt->update( 'Narf!' );
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
            'default_value' => self::DEFAULT,
            'label' => self::LABEL,
        ], $args );
        return new Option_Nonabstract( $args );
    }

}   // End of class