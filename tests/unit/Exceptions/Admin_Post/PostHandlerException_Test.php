<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mtgtools\Exceptions\Admin_Post\PostHandlerException;

class PostHandlerException_Test extends TestCase
{
    /**
     * Constants
     */
    const CODE = 500;
    const TITLE = 'Internal Server Error';

    /**
     * TEST: Can add http status attributes
     */
    public function testCanAddHttpStatus() : PostHandlerException
    {
        $exception = new PostHandlerException();
        $result = $exception->add_http_status( self::CODE, self::TITLE );

        $this->assertNull( $result );

        return $exception;
    }

    /**
     * TEST: Can get HTTP response code
     * 
     * @depends testCanAddHttpStatus
     */
    public function testCanGetHttpResponseCode( PostHandlerException $exception ) : void
    {
        $result = $exception->get_http_response_code();

        $this->assertEquals( self::CODE, $result );
    }

    /**
     * TEST: Can get HTTP response title
     * 
     * @depends testCanAddHttpStatus
     */
    public function testCanGetHttpResponseTitle( PostHandlerException $exception ) : void
    {
        $result = $exception->get_http_response_title();

        $this->assertEquals( self::TITLE, $result );
    }

    /**
     * TEST: Can get full status string
     * 
     * @depends testCanAddHttpStatus
     */
    public function testCanGetFullStatus( PostHandlerException $exception ) : void
    {
        $result = $exception->get_http_status();

        $this->assertEquals(
            self::CODE . ' ' . self::TITLE,
            $result
        );
    }

}   // End of class