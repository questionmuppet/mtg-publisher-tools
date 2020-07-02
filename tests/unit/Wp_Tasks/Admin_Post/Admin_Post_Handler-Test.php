<?php
declare(strict_types=1);

use Mtgtools\Exceptions\Admin_Post\PostHandlerException;

class Admin_Post_Handler_Test extends Admin_Post_HandlerTestCase
{
    /**
     * -------------------
     *   R E S P O N S E
     * -------------------
     */

    /**
     * TEST: Sends result to reponder on success
     */
    public function testSendsResultToResponderOnSuccess() : void
    {
        $handler = $this->create_handler();

        $this->responder->expects( $this->once() )
            ->method('handle_success')
            ->with( $this->equalTo( self::CALLBACK_RESULT ) );    

        $handler->process_action();
    }

    /**
     * TEST: Sends PostHandlerException to responder on error
     */
    public function testSendsExceptionToResponderOnError() : void
    {
        $handler = $this->create_handler();

        $this->processor->method('process_request')->willThrowException( new PostHandlerException() );

        $this->responder->expects( $this->once() )
            ->method('handle_error')
            ->with( $this->isInstanceOf( PostHandlerException::class ) );

        $handler->process_action();
    }

}   // End of class