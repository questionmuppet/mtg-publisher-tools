<?php
declare(strict_types=1);

use SteveGrunwell\PHPUnit_Markup_Assertions\MarkupAssertionsTrait;
use Mtgtools\Dashboard\Dashboard_Tab;
use Mtgtools\Mtgtools_Enqueue;

class Dashboard_TabTest extends Mtgtools_UnitTestCase
{
    /**
     * Include markup assertions
     */
    use MarkupAssertionsTrait;

    /**
     * Dashboard tab object
     */
    private $tab;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->tab = new Dashboard_Tab([
            'id'      => 'foo_bar',
            'title'   => 'Foo Bar',
            'scripts' => [
                [
                    'key'  => 'fake_script',
                    'path' => 'path/to/fake/script.js',
                ],
            ],
            'styles'  => [
                [
                    'key'  => 'fake_style',
                    'path' => 'path/to/fake/style.css',
                ],
            ],
        ]);
    }

    /**
     * TEST: Can enqueue assets
     */
    public function testCanEnqueueAssets() : void
    {
        $enqueue = $this->get_mock_enqueue();

        $result = $this->tab->enqueue_assets( $enqueue );

        $this->assertNull( $result );
    }

    /**
     * TEST: Can get href attribute
     */
    public function testCanGetHref() : string
    {
        $href = $this->tab->get_href();
        $pattern = sprintf( '/page=%s&tab=foo_bar$/', MTGTOOLS__ADMIN_SLUG );

        $this->assertRegExp( $pattern, $href );
        
        return $href;
    }

    /**
     * TEST: Inactive tab outputs correct markup
     */
    public function testInactiveTabOutputsCorrectMarkup() : void
    {
        ob_start();
        $this->tab->output_nav_tab( 'fake' );

        $html = ob_get_clean();

        $this->assertContainsSelector( 'a.nav-tab', $html );
        $this->assertNotContainsSelector( 'a.nav-tab-active', $html );
    }
    
    /**
     * TEST: Active tab outputs correct markup
     */
    public function testActiveTabOutputsCorrectMarkup() : void
    {
        ob_start();
        $this->tab->output_nav_tab( 'foo_bar' );

        $html = ob_get_clean();

        $this->assertContainsSelector( 'a.nav-tab.nav-tab-active', $html );
    }

}   // End of class