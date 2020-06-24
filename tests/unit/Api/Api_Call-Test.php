<?php
declare(strict_types=1);
use Mtgtools\Api\Api_Call;
use Mtgtools\Api\Http_Request;
use Mtgtools\Exceptions\Api as Exceptions;

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
        $this->request = $this->createMock( Http_Request::class );
    }

    /**
     * TEST: Can get result of successful api call
     */
    public function testCanGetResultOfSuccessfulApiCall() : void
    {
        $response = $this->get_dummy_response_data();
        $this->set_request_response([
            'body'    => json_encode( $response ),
            'code'    => '200',
            'message' => 'OK',
        ]);

        $api_call = new Api_Call( $this->request );
        
        $this->assertEqualsCanonicalizing( $response, $api_call->get_result() );
    }

    /**
     * TEST: Unsuccessful api call throws ApiStatusException
     */
    public function testUnsuccessfulHttpStatusThrowsApiStatusException() : void
    {
        $this->set_request_response([
            'code'    => '504',
            'message' => 'Gateway Timeout',
        ]);
        $api_call = new Api_Call( $this->request );

        $this->expectException( Exceptions\ApiStatusException::class );
        
        $api_call->get_result();
    }

    /**
     * TEST: Malformed JSON response throws ApiJsonException
     */
    public function testMalformedJsonResponseThrowsApiJsonException() : void
    {
        $this->set_request_response([
            'body'    => '',
            'code'    => '200',
            'message' => 'OK',
        ]);
        $api_call = new Api_Call( $this->request );

        $this->expectException( Exceptions\ApiJsonException::class );

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
            'url'     => '',
        ], $args);
        $this->request->method('get_response_body')->willReturn( $args['body'] );
        $this->request->method('get_status_code')->willReturn( $args['code'] );
        $this->request->method('get_status_message')->willReturn( $args['message'] );
        $this->request->method('get_sanitized_url')->willReturn( $args['url'] );
    }

    /**
     * Get dummy response data
     */
    private function get_dummy_response_data() : array
    {
        return array( 'testKey' => 'testValue' );
    }

}   // End of test class