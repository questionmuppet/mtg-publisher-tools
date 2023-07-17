<?php
declare(strict_types=1);

use Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase;
use Mtgtools\Mtgtools_Setup;
use Mtgtools\Mtgtools_Plugin;

class Mtgtools_Setup_Test extends Mtgtools_UnitTestCase
{
    /**
     * Setup instance
     */
    private $setup;

    /**
     * Mock dependencies
     */
    private $plugin;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->plugin = $this->createMock( Mtgtools_Plugin::class );
        $this->setup = new Mtgtools_Setup( $this->plugin );
    }

    /**
     * TEST: Can activate plugin
     */
    public function testCanActivatePlugin() : void
    {
        $result = $this->setup->activate();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can deactivate plugin
     */
    public function testCanDeactivatePlugin() : void
    {
        $result = $this->setup->deactivate();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can uninstall plugin
     */
    public function testCanUninstallPlugin() : void
    {
        $result = $this->setup->uninstall();

        $this->assertNull( $result );
    }

}   // End of class