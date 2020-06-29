<?php
declare(strict_types=1);

use Mtgtools\Tasks\Enqueue\Css_Asset;

class Css_Asset_Test extends Mtgtools_UnitTestCase
{
    /**
     * TEST: Can enqueue
     */
    public function testCanEnqueue() : void
    {
        $asset = new Css_Asset([
            'key'  => 'fake_style',
            'path' => 'path/to/fake/style.css',
        ]);

        $result = $asset->enqueue();

        $this->assertNull( $result );
    }

}   // End of class