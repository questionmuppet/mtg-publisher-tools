<?php
declare(strict_types=1);

namespace Mtgtools\Wp_Tasks\Admin_Post
{
    /**
     * WordPress mock functions
     */
    function wp_send_json_success( array $result )
    {
        Ajax_Responder_Test::augment_call_count('success');
        return 'We succeeded and died.';
    }
    function wp_send_json_error( array $result )
    {
        Ajax_Responder_Test::augment_call_count('error');
        return 'We failed and died.';
    }

    /**
     * Test class
     */
    class Ajax_Responder_Test extends \Mtgtools_UnitTestCase
    {
        /**
         * Call counter
         */
        use \FunctionCallCounterTrait;

        /**
         * Responder object
         */
        private $responder;

        /**
         * Setup
         */
        public function setUp() : void
        {
            parent::setUp();
            $this->responder = new Ajax_Responder();
            self::reset_call_counters([
                'success',
                'error',
            ]);
        }

        /**
         * -------------
         *   T E S T S
         * -------------
         */

        /**
         * TEST: Can handle success state
         */
        public function testCanHandleSuccess() : void
        {
            $data = [ 'Here is some data' ];

            $this->responder->handle_success( $data );

            $this->assertEquals( 1, self::get_call_count('success') );
        }

        /**
         * TEST: Can handle error state
         */
        public function testCanHandleError() : void
        {
            $error = new \RuntimeException( "An error message" );

            $this->responder->handle_error( $error );

            $this->assertEquals( 1, self::get_call_count('error') );
        }

        /**
         * TEST: is_ajax() returns true
         */
        public function testIsAjaxReturnsTrue() : void
        {
            $is_ajax = $this->responder->is_ajax();

            $this->assertTrue( $is_ajax );
        }

    }   // End of class

}   // End of namespace