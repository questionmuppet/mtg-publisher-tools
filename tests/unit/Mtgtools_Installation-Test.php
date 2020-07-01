<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Installation;
use Mtgtools\Mtgtools_Plugin;

class Mtgtools_Installation_Test extends Mtgtools_UnitTestCase
{
    /**
     * Installation instance
     */
    private $installation;

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
        $this->installation = new Mtgtools_Installation( $this->plugin );
    }

    /**
     * TEST: Can activate plugin
     */
    public function testCanActivatePlugin() : void
    {
        $result = $this->installation->activate();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can deactivate plugin
     */
    public function testCanDeactivatePlugin() : void
    {
        $result = $this->installation->deactivate();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can uninstall plugin
     */
    public function testCanUninstallPlugin() : void
    {
        $result = $this->installation->uninstall();

        $this->assertNull( $result );
    }

}   // End of class