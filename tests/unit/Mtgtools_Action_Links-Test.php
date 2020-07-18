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

}   // End of class