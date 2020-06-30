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
     * TEST: Can get task library
     * 
     * @depends testCanGetInstance
     */
    public function testCanGetTaskLibrary( Mtgtools_Plugin $instance ) : void
    {
        $library = $instance->task_library();

        $this->assertInstanceOf( Mtgtools\Task_Library::class, $library );
    }

}   // End of class