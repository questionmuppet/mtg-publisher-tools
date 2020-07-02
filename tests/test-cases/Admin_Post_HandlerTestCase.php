<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Admin_Post as Base;

class Admin_Post_HandlerTestCase extends Mtgtools_UnitTestCase
{
    /**
     * Constants
     */
    const ACTION = 'request_fake_data';
    const CALLBACK_RESULT = [];

    /**
     * Dependencies
     */
    protected $processor;
    protected $responder;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->processor = $this->createMock( Base\Admin_Request_Processor::class );
        $this->responder = $this->createMock( Base\Admin_Request_Responder::class );
    }

    /**
     * ---------------------
     *   P R O D U C E R S
     * ---------------------
     */
    protected function create_handler( array $args = [] ) : Base\Admin_Post_Handler
    {
        $args = array_replace([
            'action'   => self::ACTION,
            'callback' => function( array $args ) { return self::CALLBACK_RESULT; },
        ], $args );
        return new Base\Admin_Post_Handler( $this->processor, $this->responder, $args );
    }

}   // End of class