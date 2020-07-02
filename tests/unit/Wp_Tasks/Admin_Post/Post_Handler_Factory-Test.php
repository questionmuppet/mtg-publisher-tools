<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Admin_Post as Base;

class Post_Handler_Factory_Test extends Mtgtools_UnitTestCase
{
    /**
     * Constants
     */
    const DEFAULT_TYPE = 'ajax';

    /**
     * Factory object
     */
    private $factory;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->factory = new Base\Post_Handler_Factory();
    }

    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * TEST: Can create Admin_Request_Processor
     */
    public function testCanCreateProcessor() : void
    {
        $processor = $this->factory->create_processor();

        $this->assertInstanceOf( Base\Admin_Request_Processor::class, $processor );
    }

    /**
     * TEST: Can create Admin_Request_Responder
     */
    public function testCanCreateResponder() : void
    {
        $responder = $this->factory->create_responder( self::DEFAULT_TYPE );

        $this->assertInstanceOf( Base\Admin_Request_Responder::class, $responder );
    }

    /**
     * -----------------
     *   H A N D L E R
     * -----------------
     */

    /**
     * TEST: Can create Ajax handler
     * 
     * @depends testCanCreateProcessor
     * @depends testCanCreateResponder
     */
    public function testCanCreateAjaxHandler() : void
    {
        $handler = $this->factory->create_handler([
            'type' => 'ajax',
            'action' => 'fake_action',
            'callback' => function() {},
        ]);

        $this->assertInstanceOf( Base\Admin_Post_Handler::class, $handler );
    }

    /**
     * TEST: Can create Redirect handler
     * 
     * @depends testCanCreateProcessor
     * @depends testCanCreateResponder
     */
    public function testCanCreateRedirectHandler() : void
    {
        $handler = $this->factory->create_handler([
            'type' => 'redirect',
            'action' => 'fake_action',
            'callback' => function() {},
            'redirect_url' => '',
        ]);

        $this->assertInstanceOf( Base\Admin_Post_Handler::class, $handler );
    }

}   // End of class