<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Admin_Post\Admin_Request_Responder;

class Admin_Post_Handler_WPTest extends Admin_Post_HandlerTestCase
{
    /**
     * Constants
     */
    const POST_ACTION_HOOK   = 'admin_post_' . self::ACTION;
    const NOPRIV_ACTION_HOOK = 'admin_post_nopriv_' . self::ACTION;
    const AJAX_ACTION_HOOK   = 'wp_ajax_' . self::ACTION;
    
    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->responder->method('is_ajax')->willReturn( false );
    }

    /**
     * Teardown
     */
    public function tearDown() : void
    {
        remove_all_actions( self::POST_ACTION_HOOK );
        remove_all_actions( self::NOPRIV_ACTION_HOOK );
        remove_all_actions( self::AJAX_ACTION_HOOK );
        parent::tearDown();
    }

    /**
     * ---------------------------
     *   A C T I O N   H O O K S
     * ---------------------------
     */
    
    /**
     * TEST: Can add WP hooks
     */
    public function testCanAddHooks() : void
    {
        $handler = $this->create_handler();

        $result = $handler->add_hooks();

        $this->assertNull( $result );
    }

    /**
     * TEST: Registers correct hooks with WordPress
     * 
     * @depends testCanAddHooks
     */
    public function testRegistersCorrectHooks() : void
    {
        $handler = $this->create_handler();

        $handler->add_hooks();

        $this->assertTrue(
            has_action( self::POST_ACTION_HOOK ),
            'Failed to assert that hook action was registered.'
        );
        $this->assertFalse(
            has_action( self::NOPRIV_ACTION_HOOK ),
            'Failed to assert that "nopriv" hook action was omitted by default.'
        );
    }

    /**
     * TEST: Can register nopriv hook
     * 
     * @depends testRegistersCorrectHooks
     */
    public function testCanRegisterNoprivHook() : void
    {
        $handler = $this->create_handler([ 'nopriv' => true ]);
        
        $handler->add_hooks();

        $this->assertTrue(
            has_action( self::NOPRIV_ACTION_HOOK ),
            'Failed to assert that allowing public access registers the "nopriv" hook action.'
        );
    }

    /**
     * TEST: Ajax response type registers ajax action
     * 
     * testRegistersCorrectHooks
     */
    public function testAjaxResponseTypeRegistersAjaxAction() : void
    {
        $this->responder = $this->createMock( Admin_Request_Responder::class );
        $this->responder->method('is_ajax')->willReturn( true );
        $handler = $this->create_handler();

        $handler->add_hooks();

        $this->assertTrue(
            has_action( self::AJAX_ACTION_HOOK ),
            'Failed to assert that an Ajax response type caused an Ajax hook action to be registered.'
        );
    }

}   // End of class