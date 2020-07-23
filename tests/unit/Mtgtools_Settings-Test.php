<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Settings;
use Mtgtools\Mtgtools_Plugin;
use Mtgtools\Wp_Tasks\Options\Options_Manager;

class Mtgtools_Settings_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const TEST_OPTION = 'fake_option';

    /**
     * Instantiated SUT
     */
    private $settings;

    /**
     * Dependencies
     */
    private $options_manager;
    private $plugin;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->options_manager = $this->createMock( Options_Manager::class );
        $this->plugin = $this->createMock( Mtgtools_Plugin::class );
        $this->settings = new Mtgtools_Settings( $this->options_manager, $this->plugin );
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

}   // End of class