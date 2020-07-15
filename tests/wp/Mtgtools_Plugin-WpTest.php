<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Plugin;

class Mtgtools_Plugin_WPTest extends Mtgtools_UnitTestCase
{
    /**
     * TEST: Can get instance
     */
    public function testCanGetInstance() : Mtgtools_Plugin
    {
        $instance = Mtgtools_Plugin::get_instance();

        $this->assertInstanceOf( Mtgtools_Plugin::class, $instance );

        return $instance;
    }

    /**
     * -----------------
     *   M O D U L E S
     * -----------------
     */

    /**
     * TEST: Can get symbols module
     * 
     * @depends testCanGetInstance
     */
    public function testCanGetSymbolsModule( Mtgtools_Plugin $instance ) : void
    {
        $module = $instance->symbols();

        $this->assertInstanceOf( Mtgtools\Mtgtools_Symbols::class, $module );
    }

    /**
     * TEST: Can get dashboard module
     * 
     * @depends testCanGetInstance
     */
    public function testCanGetDashboardModule( Mtgtools_Plugin $instance ) : void
    {
        $module = $instance->dashboard();

        $this->assertInstanceOf( Mtgtools\Mtgtools_Dashboard::class, $module );
    }

    /**
     * TEST: Can get updates module
     * 
     * @depends testCanGetInstance
     */
    public function testCanGetUpdatesModule( Mtgtools_Plugin $instance ) : void
    {
        $module = $instance->updates();

        $this->assertInstanceOf( Mtgtools\Mtgtools_Updates::class, $module );
    }

    /**
     * TEST: Can get settings module
     * 
     * @depends testCanGetInstance
     */
    public function testCanGetSettingsModule( Mtgtools_Plugin $instance ) : void
    {
        $module = $instance->settings();

        $this->assertInstanceOf( Mtgtools\Mtgtools_Settings::class, $module );
    }

    /**
     * TEST: Can get images module
     * 
     * @depends testCanGetInstance
     */
    public function testCanGetImagesModule( Mtgtools_Plugin $instance ) : void
    {
        $module = $instance->images();

        $this->assertInstanceOf( Mtgtools\Mtgtools_Images::class, $module );
    }

    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * TEST: Can get task library
     * 
     * @depends testCanGetInstance
     */
    public function testCanGetTaskLibrary( Mtgtools_Plugin $instance ) : void
    {
        $library = $instance->wp_tasks();

        $this->assertInstanceOf( Mtgtools\Wp_Task_Library::class, $library );
    }

}   // End of class