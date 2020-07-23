<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Cron;
use Mtgtools\Mtgtools_Plugin;
use Mtgtools\Mtgtools_Updates;

class Mtgtools_Cron_DbTest extends WP_UnitTestCase
{
    /**
     * Constants
     */
    const HOOK = Mtgtools_Cron::CRON_HOOK;
    const CHECK_OPTION = 'mtgtools_check_for_updates';
    const CRON_PERIOD = 'weekly';

    /**
     * SUT module
     */
    private $cron;

    /**
     * Dependencies
     */
    private $plugin;
    private $updates;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->plugin = $this->createMock( Mtgtools_Plugin::class );
        $this->updates = $this->createMock( Mtgtools_Updates::class );
        $this->updates->method('get_update_period')->willReturn( self::CRON_PERIOD );
        $this->cron = new Mtgtools_Cron( $this->updates, $this->plugin );
    }

    /**
     * TEST: Can add hooks
     */
    public function testCanAddHooks() : void
    {
        $result = $this->cron->add_hooks();

        $this->assertNull( $result );
    }

    /**
     * -----------------------
     *   S C H E D U L I N G
     * -----------------------
     */

    /**
     * TEST: Can schedule update checks
     * 
     * @depends testCanAddHooks
     */
    public function testCanScheduleUpdateChecks() : void
    {
        $this->cron->add_hooks();

        $this->cron->schedule_update_checks();

        $this->assertNotFalse(
            wp_next_scheduled( self::HOOK )
        );
    }

    /**
     * TEST: Can cancel update checks
     * 
     * @depends testCanScheduleUpdateChecks
     */
    public function testCanCancelUpdateChecks() : void
    {
        $this->cron->add_hooks();
        $this->cron->schedule_update_checks();

        $this->cron->cancel_update_checks();

        $this->assertFalse(
            wp_next_scheduled( self::HOOK )
        );
    }

    /**
     * TEST: Can reset schedule to true
     * 
     * @depends testCanCancelUpdateChecks
     */
    public function testCanResetScheduleToTrue() : void
    {
        $this->cron->add_hooks();
        $this->cron->schedule_update_checks();

        $this->cron->set_schedule( true );

        $this->assertNotFalse(
            wp_next_scheduled( self::HOOK )
        );
    }

    /**
     * TEST: Can reset schedule to false
     * 
     * @depends testCanCancelUpdateChecks
     */
    public function testCanResetScheduleToFalse() : void
    {
        $this->cron->add_hooks();
        $this->cron->schedule_update_checks();

        $this->cron->set_schedule( false );

        $this->assertFalse(
            wp_next_scheduled( self::HOOK )
        );
    }

    /**
     * ---------------------------
     *   O P T I O N   H O O K S
     * ---------------------------
     */
    
    /**
     * TEST: Checks-enabled option trigger can set schedule
     * 
     * @depends testCanResetScheduleToTrue
     */
    public function testChecksEnabledOptionTriggerCanSetSchedule() : void
    {
        $this->cron->add_hooks();
        $this->cron->on_checks_enabled_change( true, false );

        $this->assertNotFalse( wp_next_scheduled( self::HOOK ) );
    }

}   // End of class