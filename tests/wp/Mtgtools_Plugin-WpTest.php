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
     * TEST: Can get modules
     * 
     * @depends testCanGetInstance
     */
    public function testCanGetModules( Mtgtools_Plugin $instance ) : void
    {
        $this->assertInstanceOf( Mtgtools\Mtgtools_Symbols::class, $instance->symbols(), 'Could not retreive the symbols module.' );
        $this->assertInstanceOf( Mtgtools\Mtgtools_Dashboard::class, $instance->dashboard(), 'Could not retreive the dashboard module.' );
        $this->assertInstanceOf( Mtgtools\Mtgtools_Updates::class, $instance->updates(), 'Could not retreive the updates module.' );
        $this->assertInstanceOf( Mtgtools\Mtgtools_Settings::class, $instance->settings(), 'Could not retreive the settings module.' );
        $this->assertInstanceOf( Mtgtools\Mtgtools_Images::class, $instance->images(), 'Could not retreive the images module.' );
        $this->assertInstanceOf( Mtgtools\Mtgtools_Action_Links::class, $instance->action_links(), 'Could not retreive the action_links module.' );
        $this->assertInstanceOf( Mtgtools\Mtgtools_Editor::class, $instance->editor(), 'Could not retreive the editor module.' );
        $this->assertInstanceOf( Mtgtools\Mtgtools_Cron::class, $instance->cron(), 'Could not retreive the cron module.' );
        $this->assertInstanceOf( Mtgtools\Mtgtools_Setup::class, $instance->setup(), 'Could not retreive the setup module.' );
    }

    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * TEST: Can get options manager
     * 
     * @depends testCanGetInstance
     */
    public function testCanGetOptionsManager( Mtgtools_Plugin $instance ) : void
    {
        $manager = $instance->options_manager();

        $this->assertInstanceOf( Mtgtools\Wp_Tasks\Options\Options_Manager::class, $manager );
    }

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