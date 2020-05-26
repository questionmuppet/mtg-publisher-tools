<?php
declare(strict_types=1);
use Mtgtools\Api\Api_Call;

class Api_CallTest extends WP_UnitTestCase
{
    /**
     * Request stub object
     */
    protected $request;

    /**
     * Common set-up for all tests
     */
    public function setUp() : void
    {
        $this->request = $this->createMock( \Mtgtools\Interfaces\Remote_Request::class );
    }

    /**
     * TEST: Successful api call
     */
    public function testCanGetResultOfSuccessfulApiCall() : void
    {
        $response = [
            'testKey' => 'testValue'
        ];
        $this->set_request_response([
            'body'    => $response,
            'code'    => '200',
            'message' => 'OK',
        ]);
        $api_call = new Api_Call( $this->request );
        $this->assertEqualsCanonicalizing( $response, $api_call->get_result() );
    }

    /**
     * Set request response parameters
     */
    private function set_request_response( array $args ) : void
    {
        $args = array_merge([
            'body'    => '',
            'code'    => '',
            'message' => '',
        ], $args);
        $this->request->method('get_response_body')->willReturn( $args['body'] );
        $this->request->method('get_status_code')->willReturn( $args['code'] );
        $this->request->method('get_status_message')->willReturn( $args['message'] );
    }

}   // End of test class