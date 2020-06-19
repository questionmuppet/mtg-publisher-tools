<?php
declare(strict_types=1);
use Mtgtools\Mtgtools_Enqueue;

class Mtgtools_EnqueueTest extends Mtgtools_UnitTestCase
{
    /**
     * Enqueue object
     */
    private $enqueue;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->enqueue = new Mtgtools_Enqueue();
    }

    /**
     * TEST: Can add style
     */
    public function testCanAddStyle() : void
    {
        $result = $this->enqueue->add_style([
            'key'  => 'fake_style',
            'path' => 'path/to/fake/style.css',
        ]);

        $this->assertNull( $result );
    }

    /**
     * TEST: Can add script
     */
    public function testCanAddScript() : void
    {
        $result = $this->enqueue->add_script([
            'key'  => 'fake_script',
            'path' => 'path/to/fake/script.css',
        ]);

        $this->assertNull( $result );
    }

}   // End of class