<?php
declare(strict_types=1);
use Mtgtools\Mtgtools_Plugin;

class Mtgtools_PluginTest extends Mtgtools_UnitTestCase
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
     * TEST: Can get symbol module
     * 
     * @depends testCanGetInstance
     */
    public function testCanGetSymbolModule( Mtgtools_Plugin $instance ) : void
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
     * TEST: Can get enqueue module
     * 
     * @depends testCanGetInstance
     */
    public function testCanGetEnqueueModule( Mtgtools_Plugin $instance ) : void
    {
        $module = $instance->enqueue();

        $this->assertInstanceOf( Mtgtools\Mtgtools_Enqueue::class, $module );
    }

}   // End of class