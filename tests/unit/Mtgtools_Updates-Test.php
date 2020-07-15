<?php
declare(strict_types=1);

use Mtgtools\Mtgtools_Updates;
use Mtgtools\Symbols\Symbol_Db_Ops;
use Mtgtools\Interfaces\Mtg_Data_Source;
use Mtgtools\Mtgtools_Plugin;
use Mtgtools\Mtgtools_Dashboard;
use Mtgtools\Exceptions\Api\ApiException;
use Mtgtools\Updates\Db_Update_Checker;

class Mtgtools_Updates_Test extends Mtgtools_UnitTestCase
{
    /**
     * Constants
     */
    const TRANSIENT = Mtgtools_Updates::TRANSIENT;
    const RECORDS_TO_ADD = [
        'fake_item_1',
        'fake_item_2',
    ];

    /**
     * Updates module instance
     */
    private $updates;

    /**
     * Mock dependencies
     */
    private $db_ops;
    private $source;
    private $plugin;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->db_ops = $this->createMock( Symbol_Db_Ops::class );
        $this->source = $this->createMock( Mtg_Data_Source::class );
        $this->plugin = $this->createMock( Mtgtools_Plugin::class );
        $this->updates = new Mtgtools_Updates( $this->db_ops, $this->source, $this->plugin );
    }

    /**
     * Teardown
     */
    public function tearDown() : void
    {
        delete_transient( self::TRANSIENT );
        parent::tearDown();
    }

    /**
     * -------------
     *   T E S T S
     * -------------
     */

    /**
     * TEST: Can add hooks
     */
    public function testCanAddHooks() : void
    {
        $result = $this->updates->add_hooks();

        $this->assertNull( $result );
    }
    
    /**
     * TEST: Can add dashboard tab
     */
    public function testCanAddDashTab() : void
    {
        $dashboard = $this->createMock( Mtgtools_Dashboard::class );
        
        $result = $this->updates->add_dash_tab( $dashboard );
        
        $this->assertNull( $result );
    }

    /**
     * TEST: Can get status info
     */
    public function testCanGetStatusInfo() : void
    {
        $info = $this->updates->get_status_info();

        $this->assertIsArray( $info );
    }
    
    /**
     * TEST: Can get linked source name
     */
    public function testCanGetNiceSourceLink() : void
    {
        $link = $this->updates->get_nice_source_link();

        $this->assertIsString( $link );
    }

    /**
     * TEST: Can print notices
     */
    public function testCanPrintNotices() : void
    {
        // Use live settings module
        $settings = Mtgtools\Mtgtools_Plugin::get_instance()->settings();
        $this->plugin->method('settings')->willReturn( $settings );
        $result = $this->updates->print_notices();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can disable notices
     */
    public function testCanDisableNotices() : void
    {
        $result = $this->updates->disable_notices();

        $this->assertIsArray( $result );
    }

    /**
     * -----------------
     *   U P D A T E S
     * -----------------
     */

    /**
     * TEST: Can update symbols
     */
    public function testCanUpdateSymbols() : void
    {
        $this->source->method('get_mana_symbols')->willReturn( $this->get_mock_symbols(2) );
        $this->db_ops->method('add_symbol')->willReturn( true );

        $result = $this->updates->update_symbols();

        $this->assertIsArray( $result );
        $this->assertEquals( 'updated', $result['action'], 'Failed to assert that the "updated" action is passed back to the admin-post handler on success.' );
    }
    
    /**
     * TEST: Deletes transient when updating
     * 
     * @depends testCanUpdateSymbols
     */
    public function testDeletesTransientOnUpdate() : void
    {
        set_transient( self::TRANSIENT, true, HOUR_IN_SECONDS );
        $this->source->method('get_mana_symbols')->willReturn( $this->get_mock_symbols(2) );
        $this->db_ops->method('add_symbol')->willReturn( true );

        $result = $this->updates->update_symbols();

        $this->assertFalse( get_transient( self::TRANSIENT ), 'Failed to assert that updating deletes transient.' );
    }

    /**
     * TEST: Updating symbols returns correct action on failure
     * 
     * @depends testCanUpdateSymbols
     */
    public function testUpdatingReturnsCorrectActionOnFailure() : void
    {
        $this->source->method('get_mana_symbols')->willReturn( $this->get_mock_symbols(2) );
        $this->db_ops->method('add_symbol')->willReturn( false );

        $result = $this->updates->update_symbols();

        $this->assertEquals( 'checked_current', $result['action'], 'Failed to assert that the "checked_current" action is passed back to the admin-post handler on failure.' );
    }

    /**
     * TEST: ApiException results in update failure
     * 
     * @depends testCanUpdateSymbols
     */
    public function testApiExceptionResultsInUpdateFailure() : void
    {
        $this->source->method('get_mana_symbols')->willThrowException( new ApiException() );
        
        $result = $this->updates->update_symbols();

        $this->assertEquals( 'failed', $result['action'], 'Failed to assert that a thrown ApiException resulted in a failed update action.' );
    }

    /**
     * -----------------
     *   C H E C K E R
     * -----------------
     */

    /**
     * TEST: Can check for updates
     */
    public function testCanCheckForUpdates() : void
    {
        $result = $this->updates->check_for_updates();

        $this->assertIsArray( $result );
        $this->assertStringContainsString( 'checked_', $result['action'], 'Failed to assert that an updates check resulted in a "checked" action result.' );
    }

    /**
     * TEST: ApiException results in check failure
     * 
     * @depends testCanCheckForUpdates
     */
    public function testApiExceptionResultsInCheckFailure() : void
    {
        $this->source->method('get_mana_symbols')->willThrowException( new ApiException() );

        $result = $this->updates->check_for_updates();

        $this->assertEquals( 'failed', $result['action'], 'Failed to assert that a thrown ApiException resulted in a failed check action.' );
    }

    /**
     * TEST: Sets transient when updates available
     * 
     * @depends testCanCheckForUpdates
     */
    public function testSetsTransientWhenUpdatesAvailable() : void
    {
        delete_transient( self::TRANSIENT );
        $checker = $this->createMock( Db_Update_Checker::class );
        $checker->method('records_to_add')->willReturn( [ 'fake_item_1', 'fake_item_2' ] );
        $this->db_ops->method('get_update_checker')->willReturn( $checker );

        $this->updates->check_for_updates();
        $transient = get_transient( self::TRANSIENT );

        $this->assertIsArray( $transient, 'Failed to assert that checking for updates sets transient when updates are available.' );
        $this->assertEqualsCanonicalizing( [ 'add' => self::RECORDS_TO_ADD ], $transient, 'Failed to assert that the expected pending records appear in the update transient.' );
    }

    /**
     * TEST: Can get updates-pending value
     */
    public function testCanGetUpdatesPending() : void
    {
        $has_updates = $this->updates->updates_pending();

        $this->assertIsBool( $has_updates );
    }

}   // End of class