<?php
declare(strict_types=1);

use Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase;
use Mtgtools\Wp_Tasks\Admin_Post as Base;

class Post_Handler_Factory_Test extends Mtgtools_UnitTestCase
{
    /**
     * Factory object
     */
    private $postHandlerFactory;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->postHandlerFactory = new Base\Post_Handler_Factory();
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
        $processor = $this->postHandlerFactory->create_processor();

        $this->assertInstanceOf( Base\Admin_Request_Processor::class, $processor );
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
     */
    public function testCanCreateAjaxHandler() : void
    {
        $handler = $this->postHandlerFactory->create_handler([
            'type' => 'ajax',
            'action' => 'fake_action',
            'callback' => function() {},
        ]);

        $this->assertInstanceOf( Base\Ajax_Handler::class, $handler );
    }

    /**
     * TEST: Can create Redirect handler
     * 
     * @depends testCanCreateProcessor
     */
    public function testCanCreateRedirectHandler() : void
    {
        $handler = $this->postHandlerFactory->create_handler([
            'type' => 'redirect',
            'action' => 'fake_action',
            'callback' => function() {},
            'redirect_url' => '',
        ]);

        $this->assertInstanceOf( Base\Redirect_Handler::class, $handler );
    }

}   // End of class