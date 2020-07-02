<?php
declare(strict_types=1);

namespace Mtgtools\Wp_Tasks\Admin_Post
{
    use Mtgtools\Exceptions\Admin_Post\PostHandlerException;

    /**
     * WordPress mock functions
     */
    function wp_send_json_success( array $result )
    {
        Ajax_Handler_WPTest::augment_call_count('success');
        return 'We succeeded and died.';
    }
    function wp_send_json_error( array $result )
    {
        Ajax_Handler_WPTest::augment_call_count('error');
        return 'We failed and died.';
    }

    /**
     * Test class
     */
    class Ajax_Handler_WPTest extends \Mtgtools_UnitTestCase
    {
        /**
         * Call counter
         */
        use \FunctionCallCounterTrait;

        /**
         * Constants
         */
        const ACTION       = 'request_fake_data';
        const PRIVATE_HOOK = 'wp_ajax_' . self::ACTION;
        const NOPRIV_HOOK  = 'wp_ajax_nopriv_' . self::ACTION;

        /**
         * Setup
         */
        public function setUp() : void
        {
            parent::setUp();
            $this->processor = $this->createMock( Admin_Request_Processor::class );
            self::reset_call_counters([
                'success',
                'error',
            ]);
        }

        /**
         * Teardown
         */
        public function tearDown() : void
        {
            remove_all_actions( self::PRIVATE_HOOK );
            remove_all_actions( self::NOPRIV_HOOK );
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
         * TEST: Success state sends result via json
         */
        public function testSuccessStateSendsJson() : void
        {
            $handler = $this->create_handler();

            $handler->process_action();

            $this->assertEquals( 1, self::get_call_count('success') );
        }

        /**
         * TEST: Error state sends result via json
         */
        public function testErrorStateSendsJson() : void
        {
            $this->processor->method('process_request')->willThrowException( new PostHandlerException() );
            $handler = $this->create_handler();

            $handler->process_action();

            $this->assertEquals( 1, self::get_call_count('error') );
        }

        /**
         * ---------------------
         *   P R O D U C E R S
         * ---------------------
         */

        /**
         * Create handler
         */
        private function create_handler( array $args = [] ) : Ajax_Handler
        {
            $args = array_replace([
                'action' => self::ACTION,
                'callback' => function() { return []; },
            ], $args );
            return new Ajax_Handler( $args, $this->processor );
        }

    }   // End of class

}   // End of namespace