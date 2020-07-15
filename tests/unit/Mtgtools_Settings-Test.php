<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Settings;
use Mtgtools\Mtgtools_Plugin;
use Mtgtools\Wp_Tasks\Options\Option_Factory;
use Mtgtools\Wp_Tasks\Options\Option;

class Mtgtools_Settings_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const TEST_OPTION = 'fake_option';

    /**
     * Instantiated module
     */
    private $settings;

    /**
     * Dependencies
     */
    private $option_factory;
    private $plugin;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->option_factory = $this->createMock( Option_Factory::class );
        $this->plugin = $this->createMock( Mtgtools_Plugin::class );
        $this->settings = new Mtgtools_Settings( $this->option_factory, $this->plugin );
    }

    /**
     * TEST: Can add hooks
     */
    public function testCanAddHooks() : void
    {
        $result = $this->settings->add_hooks();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can register settings
     */
    public function testCanRegisterSettings() : void
    {
        $result = $this->settings->register_settings();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can add setting section definition
     */
    public function testCanAddSectionDefinition() : void
    {
        $result = $this->settings->add_setting_section([]);

        $this->assertNull( $result );
    }

    /**
     * TEST: Can add option definition
     */
    public function testCanAddOptionDefinition() : void
    {
        $result = $this->settings->add_plugin_option([
            'id' => self::TEST_OPTION,
        ]);

        $this->assertNull( $result );
    }

    /**
     * TEST: Adding option without id throws DomainException
     * 
     * @depends testCanAddOptionDefinition
     */
    public function testAddingOptionWithoutIdThrowsDomainException() : void
    {
        $this->expectException( \DomainException::class );

        $this->settings->add_plugin_option([]);
    }

    /**
     * TEST: Can get option
     * 
     * @depends testCanAddOptionDefinition
     */
    public function testCanGetOption() : void
    {
        $this->settings->add_plugin_option([ 'id' => self::TEST_OPTION ]);

        $opt = $this->settings->get_plugin_option( self::TEST_OPTION );

        $this->assertInstanceOf( Option::class, $opt );
    }

    /**
     * TEST: Requesting non-existent option throws OutOfRangeException
     */
    public function testRequestingNonExistentOptionThrowsOutOfRangeException() : void
    {
        $this->expectException( \OutOfRangeException::class );

        $opt = $this->settings->get_plugin_option( 'fake-non-existent-option' );
    }

}   // End of class