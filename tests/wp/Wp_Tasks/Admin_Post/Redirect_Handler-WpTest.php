<?php
declare(strict_types=1);

namespace Mtgtools\Wp_Tasks\Admin_Post
{
    use Mtgtools\Exceptions\Admin_Post\PostHandlerException;

    /**
     * WordPress mock functions
     */

    /**
     * Test class
     */
    class Redirect_Handler_WPTest extends \Mtgtools_UnitTestCase
    {
        /**
         * WP redirect assertions
         */
        use \WpRedirectAssertionsTrait;

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
            $this->processor = $this->createMock( Admin_Request_Processor::class );
            $this->register_redirect_handler();
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
            catch ( \WpRedirectAttemptException $e ) {
                // Expected exception
            }

            $this->assertWpRedirectLocationContains( self::REDIRECT_URL );
            $this->assertWpRedirectStatusEquals( self::HTTP_STATUS );
        }

        /**
         * TEST: Error state calls wp_die()
         */
        public function testErrorStateWpDies() : void
        {
            $msg = "An error was encountered during your malformed request, you ne'er-do-well!";
            $this->processor->method('process_request')->willThrowException( new PostHandlerException( $msg ) );
            $handler = $this->create_handler();

            $this->setExpectedException( 'WPDieException', "ne'er-do-well" );

            $handler->process_action();
        }

        /**
         * ---------------------
         *   P R O V I D E R S
         * ---------------------
         */

        /**
         * Create handler
         */
        private function create_handler( array $args = [] ) : Redirect_Handler
        {
            $args = array_replace([
                'action' => self::ACTION,
                'callback' => function() { return []; },
                'redirect_url' => self::REDIRECT_URL,
            ], $args );
            return new Redirect_Handler( $args, $this->processor );
        }

    }   // End of class

}   // End of namespace