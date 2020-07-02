<?php
declare(strict_types=1);

use Mtgtools\Wp_Task_Library;
use Mtgtools\Wp_Tasks;

class Wp_Task_Library_WPTest extends Mtgtools_UnitTestCase
{
    /**
     * Task library
     */
    private $library;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->library = new Wp_Task_Library();
    }

    /**
     * TEST: Can create CSS asset
     */
    public function testCanCreateCssAsset() : void
    {
        $object = $this->library->create_style([
            'key'  => 'foo_bar',
            'path' => 'path/to/fake/style.css',
        ]);

        $this->assertInstanceOf( Wp_Tasks\Enqueue\Css_Asset::class, $object );
    }

    /**
     * TEST: Can create JS asset
     */
    public function testCanCreateJsAsset() : void
    {
        $object = $this->library->create_script([
            'key'  => 'foo_bar',
            'path' => 'path/to/fake/script.js',
        ]);

        $this->assertInstanceOf( Wp_Tasks\Enqueue\Js_Asset::class, $object );
    }

    /**
     * TEST: Can create template
     */
    public function testCanCreateTemplate() : void
    {
        $object = $this->library->create_template([
            'path' => 'path/to/fake/template.php',
        ]);

        $this->assertInstanceOf( Wp_Tasks\Templates\Template::class, $object );
    }

    /**
     * TEST: Can create table
     */
    public function testCanCreateTable() : void
    {
        $object = $this->library->create_table([
            'id'           => 'foo_bar',
            'fields'       => [
                array(),
                array()
            ],
            'row_callback' => function( $filter ) { return []; },
        ]);

        $this->assertInstanceOf( Wp_Tasks\Tables\Table_Data::class, $object );
    }

    /**
     * TEST: Can create admin notice
     */
    public function testCanCreateAdminNotice() : void
    {
        $object = $this->library->create_admin_notice([
            'message' => 'A nice notice for the user!',
        ]);

        $this->assertInstanceOf( Wp_Tasks\Notices\Admin_Notice::class, $object );
    }

    /**
     * TEST: Can create post handler
     */
    public function testCanCreatePostHandler() : void
    {
        $object = $this->library->create_post_handler([
            'type'     => 'ajax',
            'action'   => 'foo_bar',
            'callback' => function( $args ) { return []; },
        ]);

        $this->assertInstanceOf( Wp_Tasks\Admin_Post\Admin_Post_Handler:: class, $object );
    }

}   // End of class