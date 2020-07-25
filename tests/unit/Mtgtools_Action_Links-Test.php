<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Action_Links;
use Mtgtools\Mtgtools_Plugin;

class Mtgtools_Action_Links_Test extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const NUM_LINKS = 2;
    const NUM_META = 1;
    const BASENAME = 'mtg-publisher-tools/mtg-publisher-tools.php';

    /**
     * SUT module
     */
    private $links;

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
        $this->links = new Mtgtools_Action_Links( $this->plugin );
    }

    /**
     * TEST: Can add hooks
     */
    public function testCanAddHooks() : void
    {
        $result = $this->links->add_hooks();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can create action links
     */
    public function testCanCreateActionLinks() : void
    {
        $new = $this->links->add_action_links( [] );

        $this->assertCount( self::NUM_LINKS, $new, 'Failed to assert that the expected number of action links were created.' );
    }

    /**
     * TEST: Adds row-meta links to plugin
     */
    public function testAddsRowMetaLinksToPlugin() : void
    {
        $new = $this->links->add_meta_links( [], self::BASENAME );

        $this->assertCount( self::NUM_META, $new, 'Failed to assert that the expected number of row-meta links were added to the plugin.' );
    }

    /**
     * TEST: Omits row-meta links from other plugins
     * 
     * @depends testAddsRowMetaLinksToPlugin
     */
    public function testOmitsRowMetaLinksFromOtherPlugins() : void
    {
        $new = $this->links->add_meta_links( [], 'another-plugin.php' );

        $this->assertCount( 0, $new, 'Failed to assert that row-meta links were omitted from other plugins.' );
    }

}   // End of class