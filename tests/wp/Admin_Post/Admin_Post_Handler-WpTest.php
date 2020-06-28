<?php
declare(strict_types=1);

use Mtgtools\Admin_Post\Admin_Post_Handler;
use Mtgtools\Admin_Post\Interfaces\Admin_Post_Responder;
use Mtgtools\Exceptions\Admin_Post as Exceptions;

class Admin_Post_Handler_WPTest extends Mtgtools_UnitTestCase
{
    /**
     * Responder object
     */
    private $responder;

    /**
     * Prefix for action hooks
     */
    private $wp_hook_prefix = 'testAction';

    /**
     * Admin-post action
     */
    private $action = 'request_fake_data';

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->responder = $this->create_mock_responder();
        wp_get_current_user()->add_cap( 'manage_options' );
        $_POST['_wpnonce'] = wp_create_nonce( $this->action );
    }

    /**
     * Teardown
     */
    public function tearDown() : void
    {
        remove_all_actions( $this->get_action_hook() );
        remove_all_actions( $this->get_action_hook('nopriv') );
        parent::tearDown();
    }

    /**
     * Teardown after class
     */
    static public function tearDownAfterClass() : void
    {
        unset( $_POST['_wpnonce'] );
        parent::tearDownAfterClass();
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
            has_action( $this->get_action_hook() ),
            'Failed to assert that hook action was registered.'
        );
        $this->assertFalse(
            has_action( $this->get_action_hook('nopriv') ),
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
            has_action( $this->get_action_hook('nopriv') ),
            'Failed to assert that allowing public access registers the "nopriv" hook action.'
        );
    }

    /**
     * -----------------------------
     *   A U T H O R I Z A T I O N
     * -----------------------------
     */
    
    /**
     * TEST: Insufficient permissions triggers error
     */
    public function testInsufficientPermissionsTriggersError() : void
    {
        $this->responder->expects( $this->once() )
            ->method( 'handle_error' )
            ->with( $this->isInstanceOf( Exceptions\PostHandlerException::class ) );

        $handler = $this->create_handler([ 'capability' => 'fake_cap_too_high' ]);

        $handler->process_action();
    }

    /**
     * TEST: Invalid nonce triggers error
     * 
     * @depends testInsufficientPermissionsTriggersError
     */
    public function testInvalidNonceTriggersError() : void
    {
        $_POST['_wpnonce'] = 'Invalid_Nonce';
        $this->responder->expects( $this->once() )
            ->method( 'handle_error' )
            ->with( $this->isInstanceOf( Exceptions\PostHandlerException::class ) );
        
        $handler = $this->create_handler();

        $handler->process_action();
    }

    /**
     * TEST: Can process an authorized action
     * 
     * @depends testInvalidNonceTriggersError
     */
    public function testCanProcessAuthorizedAction() : void
    {
        $this->responder->expects( $this->once() )
            ->method( 'handle_success' )
            ->with( $this->isType('array') );
        
        $handler = $this->create_handler();

        $handler->process_action();
    }

    /**
     * -------------------
     *   C A L L B A C K
     * -------------------
     */

    /**
     * TEST: Successful call sends result to responder
     * 
     * @depends testCanProcessAuthorizedAction
     */
    public function testSuccessfulCallSendsResultToResponder() : void
    {
        $this->responder->expects( $this->once() )
            ->method( 'handle_success' )
            ->with( $this->equalTo( $this->get_expected_result() ) );
            
        $handler = $this->create_handler();

        $handler->process_action();
    }

    /**
     * TEST: Bad callback return value throws UnexpectedValueException
     * 
     * @depends testCanProcessAuthorizedAction
     */
    public function testBadCallbackThrowsUnexpectedValueException() : void
    {
        $callback = function( array $args ) {
            return 'A malfored string response';
        };
        $handler = $this->create_handler([ 'callback' => $callback ]);

        $this->expectException( \UnexpectedValueException::class );

        $handler->process_action();
    }

    /**
     * TEST: User args are passed to callback
     * 
     * @depends testCanProcessAuthorizedAction
     */
    public function testUserArgsPassedToCallback() : void
    {
        $_POST['return_different'] = 1;
        $this->responder->expects( $this->once() )
            ->method( 'handle_success' )
            ->with( $this->equalTo( [ 'An alternate result' ] ) );
        
        $handler = $this->create_handler([
            'user_args' => array( 'return_different' ),
        ]);

        $handler->process_action();
    }

    /**
     * ---------------------
     *   P R O D U C E R S
     * ---------------------
     */

    /**
     * Create handler
     */
    private function create_handler( array $args = [] ) : Admin_Post_Handler
    {
        $args = array_merge([
            'action'   => $this->action,
            'callback' => $this->get_dummy_callback(),
        ], $args );
        return new Admin_Post_Handler( $args, $this->responder );
    }

    /**
     * Create mock responder object
     */
    private function create_mock_responder() : Admin_Post_Responder
    {
        $responder = $this->getMockBuilder( Admin_Post_Responder::class )
            ->setMethods(['handle_success', 'handle_error', 'get_wp_prefix'])
            ->getMock();
        
        $responder->method('get_wp_prefix')->willReturn( $this->wp_hook_prefix );
        return $responder;
    }

    /**
     * Get expected result from callback
     */
    private function get_expected_result() : array
    {
        $callback = $this->get_dummy_callback();
        return call_user_func( $callback, [] );
    }
    
    /**
     * Get dummy callback function
     */
    private function get_dummy_callback() : callable
    {
        return function( array $args ) {
            $alternate = boolval( $args['return_different'] ?? '' );
            return $alternate
                ? [ 'An alternate result' ]
                : [ 'Some nice, fake result data' ];
        };
    }

    /**
     * Get action hook
     */
    private function get_action_hook( string $interfix = '' ) : string
    {
        return implode(
            '_',
            array_filter([
                $this->wp_hook_prefix,
                $interfix,
                $this->action
            ])
        );
    }

}   // End of class