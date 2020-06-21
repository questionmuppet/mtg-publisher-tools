<?php
declare(strict_types=1);
use Mtgtools\Api\Api_Call;
use Mtgtools\Exceptions\Http as Exceptions;

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
     * TEST: 500 status
     */
    public function test500HttpStatusThrows500Exception() : void
    {
        $this->expectException( Exceptions\Http500StatusException::class );
        $this->set_request_response([
            'code'    => '504',
            'message' => 'Gateway Timeout',
        ]);
        $api_call = new Api_Call( $this->request );
        $api_call->get_result();
    }

    /**
     * TEST: 400 status
     */
    public function test400HttpStatusThrows400Exception() : void
    {
        $this->expectException( Exceptions\Http400StatusException::class );
        $this->set_request_response([
            'code'    => '404',
            'message' => 'Not Found',
        ]);
        $api_call = new Api_Call( $this->request );
        $api_call->get_result();
    }

    /**
     * TEST: 400 status
     */
    public function testUnknownHttpStatusThrowsGenericException() : void
    {
        $this->expectException( Exceptions\HttpStatusException::class );
        $this->set_request_response([
            'code'    => '100',
            'message' => 'Continue',
        ]);
        $api_call = new Api_Call( $this->request );
        $api_call->get_result();
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