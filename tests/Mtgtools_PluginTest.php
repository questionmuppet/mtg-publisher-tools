<?php
declare(strict_types=1);
use Mtgtools\Mtgtools_Plugin;

class Mtgtools_PluginTest extends WP_UnitTestCase
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

        $this->assertInstanceOf( \Mtgtools\Mtgtools_Symbols::class, $module );
    }

}   // End of class