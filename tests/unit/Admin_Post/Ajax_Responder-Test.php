<?php
declare(strict_types=1);

namespace Mtgtools\Admin_Post
{
    /**
     * WordPress mock functions
     */
    function wp_send_json_success() {
        return 'We succeeded and died.';
    }
    function wp_send_json_error() {
        return 'We failed and died.';
    }

    /**
     * Test class
     */
    class Ajax_Responder_Test extends \Mtgtools_UnitTestCase
    {
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
        }

        /**
         * TEST: Can handle success state
         */
        public function testCanHandleSuccess() : void
        {
            $data = [ 'Here is some data' ];

            $result = $this->responder->handle_success( $data );

            $this->assertNull( $result );
        }

        /**
         * TEST: Can handle error state
         */
        public function testCanHandleError() : void
        {
            $error = new \RuntimeException( "An error message" );

            $result = $this->responder->handle_error( $error );

            $this->assertNull( $result );
        }

        /**
         * TEST: Can get wp_prefix
         */
        public function testCanGetWpPrefix() : void
        {
            $prefix = $this->responder->get_wp_prefix();

            $this->assertEquals( 'wp_ajax', $prefix );
        }

    }   // End of class

}   // End of namespace