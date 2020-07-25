<?php
declare(strict_types=1);

use Mtgtools\Database_Services;
use Mtgtools\Db\Services;

class Database_Services_DbTest extends WP_UnitTestCase
{
    /**
     * SUT
     */
    private $database;

    /**
     * Dependencies
     */
    private $wpdb;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->database = new Database_Services( $this->wpdb );
        $this->remove_temp_table_filters();
    }
    
    /**
     * Remove temporary table filters (allows for foreign key constraints)
     */
    private function remove_temp_table_filters() : void
    {
        remove_filter( 'query', array( $this, '_create_temporary_tables' ) );
        remove_filter( 'query', array( $this, '_drop_temporary_tables' ) );
    }

    /**
     * TEST: Can get symbols service
     */
    public function testCanGetSymbolsService() : void
    {
        $service = $this->database->symbols();

        $this->assertInstanceOf( Services\Symbol_Db_Ops::class, $service );
    }

    /**
     * TEST: Can get cards service
     */
    public function testCanGetCardsService() : void
    {
        $service = $this->database->cards();

        $this->assertInstanceOf( Services\Card_Db_Ops::class, $service );
    }

    /**
     * TEST: Can install tables
     * 
     * @depends testCanGetSymbolsService
     * @depends testCanGetCardsService
     */
    public function testCanInstallTables() : void
    {
        $result = $this->database->install();

        $this->assertNull( $result );
    }

    /**
     * TEST: Can uninstall tables
     * 
     * @depends testCanInstallTables
     */
    public function testCanUninstallTables() : void
    {
        $result = $this->database->uninstall();
        
        $this->assertNull( $result );
    }

}   // End of class