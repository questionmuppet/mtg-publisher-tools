<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Editor;
use Mtgtools\Mtgtools_Plugin;

class Mtgtools_Editor_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const NUM_PLUGINS = 1;
    const NUM_BUTTONS = 2;

    /**
     * SUT module
     */
    private $editor;

    /**
     * Dependencies
     */
    private $plugin;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->plugin = $this->createMock( Mtgtools_Plugin::class );
        $this->editor = new Mtgtools_Editor( $this->plugin );
    }

    /**
     * TEST: Can add hooks
     */
    public function testCanAddHooks() : void
    {
        $result = $this->editor->add_hooks();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can register editor buttons
     */
    public function testCanRegisterEditorButtons() : void
    {
        $array = $this->editor->register_buttons( [] );

        $this->assertCount( self::NUM_BUTTONS, $array );
    }

    /**
     * TEST: Can create tinyMCE plugin
     */
    public function testCanCreateTinyMcePlugin() : void
    {
        $array = $this->editor->add_mce_plugin( [] );

        $this->assertCount( self::NUM_PLUGINS, $array );
    }

}   // End of class