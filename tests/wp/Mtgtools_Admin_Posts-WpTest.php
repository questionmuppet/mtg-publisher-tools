<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Admin_Posts;

class Mtgtools_Admin_Posts_WPTest extends Mtgtools_UnitTestCase
{
    /**
     * Admin_Posts module
     */
    private $admin_posts;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->admin_posts = new Mtgtools_Admin_Posts( $this->get_mock_plugin() );
    }

    /**
     * Teardown
     */
    public function tearDown() : void
    {
        remove_all_actions( 'mtgtools_admin_post_handler_definitions' );
        parent::tearDown();
    }

    /**
     * TEST: Can register handlers
     */
    public function testCanRegisterHandlers() : void
    {
        $result = $this->admin_posts->register_handlers();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can create admin-post action via filter hook
     * 
     * @depends testCanRegisterHandlers
     */
    public function testCanCreateActionViaFilterHook() : void
    {
        add_filter( 'mtgtools_admin_post_handler_definitions', function( array $defs ) {
            $defs[] = array(
                'type'     => 'ajax',
                'action'   => 'fake_action',
                'callback' => function() { return []; },
            );
            return $defs;
        });

        $this->admin_posts->register_handlers();

        $this->assertTrue(
            has_action( 'wp_ajax_fake_action' ),
            'Failed to assert that an admin-post action could be created via filter hook.'
        );
    }

}   // End of class