<?php
declare(strict_types=1);
use Mtgtools\Api\Http_Request;
use Mtgtools\Exceptions\Http\HttpConnectionException;

class Http_RequestTest extends WP_UnitTestCase
{
    /**
     * TEST: Can get status code
     */
    public function testCanGetStatusCode() : void
    {
        $request = $this->create_request();

        $result = $request->get_status_code();

        $this->assertEquals( 200, $result );
    }

    /**
     * TEST: Can get status message
     */
    public function testCanGetStatusMessage() : void
    {
        $request = $this->create_request();

        $result = $request->get_status_message();

        $this->assertEquals( 'OK', $result );
    }

    /**
     * TEST: Can get response body
     */
    public function testCanGetResponseBody() : array
    {
        $request = $this->create_request();

        $result = $request->get_response_body();

        $this->assertIsArray( $result );

        return $result;
    }

    /**
     * TEST: Response body contains correct data
     * 
     * @depends testCanGetResponseBody
     */
    public function testResponseBodyContainsCorrectData( array $result ) : void
    {
        $this->assertCount( 5, $result );
        $this->assertEquals( 'id labore ex et quam laborum', $result[0]['name'] ?? '' );
    }

    /**
     * TEST: Invalid method throws DomainException
     */
    public function testInvalidMethodThrowsDomainException() : void
    {
        $request = $this->create_request([ 'method' => 'INVALID' ]);

        $this->expectException( \DomainException::class );

        $request->get_status_code();
    }

    /**
     * TEST: Bad url throws HttpConnectionException
     */
    public function testBadUrlThrowsHttpConnectionException() : void
    {
        $request = $this->create_request([ 'url' => 'http://fake-but-extremely-fake.org' ]);

        $this->expectException( HttpConnectionException::class );

        $request->get_status_code();
    }

    /**
     * Create an HTTP request
     */
    private function create_request( array $args = [] ) : Http_Request
    {
        $args = array_merge([
            'method' => 'GET',
            'url'    => 'https://jsonplaceholder.typicode.com/posts/1/comments',
        ], $args );
        return new Http_Request( $args );
    }
    
}   // End of class