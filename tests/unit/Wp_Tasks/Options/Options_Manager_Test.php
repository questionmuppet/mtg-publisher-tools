<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Options;

class Options_Manager_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const OPT_KEY = 'fake_option_key';
    const PARAMS = [];

    /**
     * Instantiated SUT
     */
    private $manager;

    /**
     * Dependencies
     */
    private $optionFactory;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->optionFactory = $this->createMock( Options\Option_Factory::class );
        $this->manager = new Options\Options_Manager( $this->optionFactory );
    }

    /**
     * TEST: Can register option
     */
    public function testCanRegisterOption() : void
    {
        $result = $this->manager->register_option( self::OPT_KEY, self::PARAMS );

        $this->assertNull( $result );
    }

    /**
     * TEST: Registering option with empty key throws exception
     * 
     * @depends testCanRegisterOption
     */
    public function testRegisteringOptionWithEmptyKeyThrowsException() : void
    {
        $this->expectException( DomainException::class );

        $this->manager->register_option( '', self::PARAMS );
    }

    /**
     * TEST: Registering duplicate option throws exception
     * 
     * @depends testRegisteringOptionWithEmptyKeyThrowsException
     */
    public function testRegisteringDuplicateOptionThrowsException() : void
    {
        $this->manager->register_option( self::OPT_KEY, self::PARAMS );

        $this->expectException( DomainException::class );

        $this->manager->register_option( self::OPT_KEY, self::PARAMS );
    }

    /**
     * TEST: Can get registered option
     * 
     * @depends testCanRegisterOption
     */
    public function testCanGetRegisteredOption() : void
    {
        $this->manager->register_option( self::OPT_KEY, self::PARAMS );

        $option = $this->manager->get_option( self::OPT_KEY );

        $this->assertInstanceOf( Options\Plugin_Option::class, $option );
    }

    /**
     * TEST: Requesting invalid option throws exception
     * 
     * @depends testCanGetRegisteredOption
     */
    public function testRequestingInvalidOptionThrowsException() : void
    {
        $this->expectException( OutOfRangeException::class );

        $this->manager->get_option( 'invalid_option_key' );
    }
    
    /**
     * TEST: Can reset option defaults
     * 
     * @depends testCanGetRegisteredOption
     */
    public function testCanResetOptionDefaults() : void
    {
        $option = $this->createMock( Options\Plugin_Option::class );
        $option
            ->expects( $this->once() )
            ->method('add_to_db');
        $this->optionFactory->method('create_option')->willReturn( $option );

        $this->manager->register_option( self::OPT_KEY, self::PARAMS );

        $this->manager->reset_defaults();
    }
    
    /**
     * TEST: Can delete options from db
     * 
     * @depends testCanGetRegisteredOption
     */
    public function testCanDeleteOptionsFromDb() : void
    {
        $option = $this->createMock( Options\Plugin_Option::class );
        $option
            ->expects( $this->once() )
            ->method('delete');

        $this->optionFactory->method('create_option')->willReturn( $option );

        $this->manager->register_option( self::OPT_KEY, self::PARAMS );

        $this->manager->delete_options();
    }

}   // End of class