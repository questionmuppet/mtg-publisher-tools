<?php
declare(strict_types=1);

use Mtgtools\Wp_Tasks\Admin_Post\Post_Handler_Factory;
use Mtgtools\Wp_Tasks\Admin_Post\Admin_Post_Handler;

class Post_Handler_Factory_Test extends Mtgtools_UnitTestCase
{
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
        $this->factory = new Post_Handler_Factory();
    }

    /**
     * TEST: Can create Ajax handler
     */
    public function testCanCreateAjaxHandler() : void
    {
        $handler = $this->factory->create_handler([
            'type'     => 'ajax',
            'action'   => 'fake_action',
            'callback' => function() {},
        ]);

        $this->assertInstanceOf( Admin_Post_Handler::class, $handler );
    }

    /**
     * TEST: Invalid type throws OutOfRangeException
     */
    public function testInvalidTypeThrowsOutOfRangeException() : void
    {
        $this->expectException( \OutOfRangeException::class );

        $handler = $this->factory->create_handler([
            'type'     => 'invalid_response_type',
            'action'   => 'fake_action',
            'callback' => function() {},
        ]);
    }

}   // End of class