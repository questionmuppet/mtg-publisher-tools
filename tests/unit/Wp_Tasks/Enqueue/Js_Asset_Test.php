<?php
declare(strict_types=1);

use Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase;
use Mtgtools\Wp_Tasks\Enqueue\Js_Asset;

class Js_Asset_Test extends Mtgtools_UnitTestCase
{
    /**
     * TEST: Can enqueue
     */
    public function testCanEnqueue() : void
    {
        $asset = new Js_Asset([
            'key'  => 'fake_script',
            'path' => 'path/to/fake/script.js',
        ]);

        $result = $asset->enqueue();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can localize with JS variables
     * 
     * @depends testCanEnqueue
     */
    public function testCanLocalizeWithVariables() : void
    {
        $asset = new Js_Asset([
            'key'  => 'fake_script',
            'path' => 'path/to/fake/script.js',
            'data' => [
                'key_one' => [],
                'key_two' => [],
            ]
        ]);

        $result = $asset->enqueue();

        $this->assertNull( $result );
    }

}   // End of class