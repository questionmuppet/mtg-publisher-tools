<?php
declare(strict_types=1);

use Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase;
use Mtgtools\Wp_Tasks\Admin_Post as Base;
use Mtgtools\Exceptions\Admin_Post\PostHandlerException;
use Mtgtools\Tests\TestCases\Exceptions\WpRedirectAttemptException;
use Mtgtools\Tests\TestCases\Traits\WpRedirectAssertionsTrait;
use Mtgtools\Tests\TestCases\Traits\WpExitAssertionsTrait;

class Redirect_Handler_WpTest extends Mtgtools_UnitTestCase
{
    /**
     * Wp assertion traits
     */
    use WpRedirectAssertionsTrait;
    use WpExitAssertionsTrait;

    /**
     * Action hooks
     */
    const ACTION       = 'request_fake_data';
    const PRIVATE_HOOK = 'admin_post_' . self::ACTION;
    const NOPRIV_HOOK  = 'admin_post_nopriv_' . self::ACTION;

    /**
     * Responses
     */
    const CALLBACK_RESULT = [ 'fake_response' ];
    const ERROR_MESSAGE = 'Ya done fucked up!';
    const REDIRECT_URL = 'http://example.org/';
    const HTTP_STATUS = 302;
    const HTTP_ERROR_STATUS = 500;
    const HTTP_ERROR_TITLE = 'Internal Server Error';

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
        $this->register_redirect_handler();
        $this->register_wp_exit_tracker();
    }

    /**
     * Teardown
     */
    public function tearDown() : void
    {
        $this->remove_wp_exit_tracker();
        parent::tearDown();
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
     * TEST: Success state redirects using correct parameters
     */
    public function testSuccessStateRedirectsUsingCorrectParameters() : void
    {
        $this->processor->method('process_request')->willReturn( self::CALLBACK_RESULT );
        $handler = $this->create_handler();

        try {
            $handler->process_action();
        }
        catch ( WpRedirectAttemptException $e ) {
            // Expected exception
        }

        $this->assertWpRedirectLocationContains( self::REDIRECT_URL );
        $this->assertWpRedirectStatusEquals( self::HTTP_STATUS );
    }

    /**
     * TEST: Error state calls wp_die()
     */
    public function testErrorStateCallsWpDie() : void
    {
        $this->processor->method('process_request')->willThrowException( new PostHandlerException() );
        $handler = $this->create_handler();

        $this->expectException( WPDieException::class );
        
        $handler->process_action();
    }

    /**
     * TEST: Error state dies with correct parameters
     * 
     * @depends testErrorStateCallsWpDie
     */
    public function testErrorStateDiesWithCorrectParams() : void
    {
        $this->processor->method('process_request')->willThrowException( new PostHandlerException( self::ERROR_MESSAGE ) );
        $handler = $this->create_handler();

        try {
            $handler->process_action();
        }
        catch ( WpDieException $e ) {
            // Expected exception
        }

        $this->assertStringContainsString( self::ERROR_MESSAGE, $this->get_wp_die_params('message'), 'Failed to assert that correct error message was passed to wp_die().' );
        $this->assertStringContainsString( self::HTTP_ERROR_TITLE, $this->get_wp_die_params('title'), 'Failed to assert that the Http status title was passed to wp_die().' );
        $this->assertEquals( self::HTTP_ERROR_STATUS, $this->get_wp_die_params('args')['response'], 'Failed to assert that the Http status code was passed to wp_die().' );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Create handler
     */
    private function create_handler( array $args = [] ) : Base\Redirect_Handler
    {
        $args = array_replace([
            'action' => self::ACTION,
            'callback' => function() { return []; },
            'redirect_url' => self::REDIRECT_URL,
        ], $args );
        return new Base\Redirect_Handler( $args, $this->processor );
    }

}   // End of class