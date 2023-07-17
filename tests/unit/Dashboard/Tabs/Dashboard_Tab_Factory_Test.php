<?php
declare(strict_types=1);

use Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab_Factory;
use Mtgtools\Dashboard\Tabs\Dashboard_Tab;

class Dashboard_Tab_Factory_Test extends Mtgtools_UnitTestCase
{
    /**
     * Factory object
     */
    private $tabFactory;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->tabFactory = new Dashboard_Tab_Factory();
    }

    /**
     * TEST: Can create dashboard tab
     */
    public function testCanCreateDashboardTab() : void
    {
        $tab = $this->tabFactory->create_tab([
            'id' => 'foo_bar',
        ]);

        $this->assertInstanceOf( Dashboard_Tab::class, $tab );
    }
}
