<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Admin_Post as Base;
use Mtgtools\Exceptions\Admin_Post\PostHandlerException;

class Ajax_Handler_WpTest extends WP_Ajax_UnitTestCase
{
    /**
     * Action hooks
     */
    const ACTION          = 'request_fake_data';
    const PRIVATE_HOOK    = 'wp_ajax_' . self::ACTION;
    const NOPRIV_HOOK     = 'wp_ajax_nopriv_' . self::ACTION;

    /**
     * Responses
     */
    const CALLBACK_RESULT = ['fake_response' ];
    const ERROR_MESSAGE = 'Ya done fucked up!';

    /**
     * Request processor
     */
    private $processor;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->processor = $this->createMock( Base\Admin_Request_Processor::class );
    }

    /**
     * ---------------------------
     *   A C T I O N   H O O K S
     * ---------------------------
     */

    /**
     * TEST: Can add hooks
     */
    public function testCanAddHooks() : void
    {
        $handler = $this->create_handler();

        $result = $handler->add_hooks();

        $this->assertNull( $result );
    }

    /**
     * TEST: Private action hook is registered
     * 
     * @depends testCanAddHooks
     */
    public function testPrivateHookIsRegistered() : void
    {
        $handler = $this->create_handler();

        $handler->add_hooks();

        $this->assertTrue(
            has_action( self::PRIVATE_HOOK ),
            'Failed to assert that private action hook was registered.'
        );
    }

    /**
     * TEST: Nopriv action hook omitted by default
     * 
     * @depends testCanAddHooks
     */
    public function testNoprivHookIsOmittedByDefault() : void
    {
        $handler = $this->create_handler();

        $handler->add_hooks();

        $this->assertFalse(
            has_action( self::NOPRIV_HOOK ),
            'Failed to assert that "nopriv" action hook was left unregistered by default.'
        );
    }

    /**
     * TEST: Can register nopriv action hook
     * 
     * @depends testNoprivHookIsOmittedByDefault
     */
    public function testCanRegisterNoprivHook() : void
    {
        $handler = $this->create_handler([ 'nopriv' => true ]);

        $handler->add_hooks();

        $this->assertTrue(
            has_action( self::NOPRIV_HOOK ),
            'Failed to assert that "nopriv" action hook could be registered via constructor args.'
        );
    }

    /**
     * -------------------
     *   R E S P O N S E
     * -------------------
     */

    /**
     * TEST: Success state sends response
     * 
     * @depends testPrivateHookIsRegistered
     */
    public function testSuccessStateSendsResponseString() : string
    {
        $this->processor->method('process_request')->willReturn( self::CALLBACK_RESULT );
        $handler = $this->create_handler();
        $handler->add_hooks();
        
        $output = '';
        try {
            $this->_handleAjax( self::ACTION );
        }
        catch( WPAjaxDieContinueException $e ) {
            $output = $this->_last_response;
        }
        
        $this->assertNotEmpty( $output, 'Failed to return response string on successful request.' );
        return $output;
    }

    /**
     * TEST: Success JSON contains correct data
     * 
     * @depends testSuccessStateSendsResponseString
     */
    public function testSuccessJsonContainsCorrectData( string $response ) : void
    {
        $expected = json_encode([
            'success' => true,
            'data' => self::CALLBACK_RESULT
        ]);

        $this->assertJsonStringEqualsJsonString( $expected, $response );
    }

    /**
     * TEST: Error state sends result
     * 
     * @depends testPrivateHookIsRegistered
     */
    public function testErrorStateSendsResponseString() : string
    {
        $this->processor->method('process_request')->willThrowException( new PostHandlerException( self::ERROR_MESSAGE ) );
        $handler = $this->create_handler();
        $handler->add_hooks();

        $output = '';
        try {
            $this->_handleAjax( self::ACTION );
        }
        catch( WPAjaxDieContinueException $e ) {
            $output = $this->_last_response;
        }
        
        $this->assertNotEmpty( $output, 'Failed to return response string on request error.' );
        return $output;
    }

    /**
     * TEST: Error JSON contains correct data
     * 
     * @depends testErrorStateSendsResponseString
     */
    public function testErrorJsonContainsCorrectData( string $response ) : void
    {
        $expected = json_encode([
            'success' => false,
            'data' => [
                'error' => self::ERROR_MESSAGE
            ]
        ]);

        $this->assertJsonStringEqualsJsonString( $expected, $response );
    }

    /**
     * ---------------------
     *   P R O D U C E R S
     * ---------------------
     */

    /**
     * Create handler
     */
    private function create_handler( array $args = [] ) : Base\Ajax_Handler
    {
        $args = array_replace([
            'action' => self::ACTION,
            'callback' => function() {},
        ], $args );
        return new Base\Ajax_Handler( $args, $this->processor );
    }

}   // End of class