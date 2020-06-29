<?php
declare(strict_types=1);

use Mtgtools\Task_Library;
use Mtgtools\Tasks;

class Task_Library_WPTest extends Mtgtools_UnitTestCase
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
        $this->library = new Task_Library();
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

        $this->assertInstanceOf( Tasks\Enqueue\Css_Asset::class, $object );
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

        $this->assertInstanceOf( Tasks\Enqueue\Js_Asset::class, $object );
    }

    /**
     * TEST: Can create template
     */
    public function testCanCreateTemplate() : void
    {
        $object = $this->library->create_template([
            'path' => 'path/to/fake/template.php',
        ]);

        $this->assertInstanceOf( Tasks\Templates\Template::class, $object );
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

        $this->assertInstanceOf( Tasks\Tables\Table_Data::class, $object );
    }

    /**
     * TEST: Can create admin notice
     */
    public function testCanCreateAdminNotice() : void
    {
        $object = $this->library->create_admin_notice([
            'message' => 'A nice notice for the user!',
        ]);

        $this->assertInstanceOf( Tasks\Notices\Admin_Notice::class, $object );
    }

}   // End of class