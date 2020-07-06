<?php
declare(strict_types=1);

use Mtgtools\Updates\Db_Update_Checker;
use Mtgtools\Interfaces\Hash_Map;

class Db_Update_Checker_DbTest extends Mtgtools_UnitTestCase
{
    /**
     * Constants
     */
    const DB_TABLE = 'mtgtools_comparison_TEST';
    const KEY_COLUMN = 'test_id';
    const HASH_COLUMN = 'test_hash';

    /**
     * Instantiated checker
     */
    private $checker;

    /**
     * Dependencies
     */
    private $db;
    private $hash_map;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        global $wpdb;
        $this->db = $wpdb;
        $this->hash_map = $this->createMock( Hash_Map::class );
        $this->checker = new Db_Update_Checker( $this->db, $this->hash_map, [
            'db_table' => self::DB_TABLE,
            'key_column' => self::KEY_COLUMN,
            'hash_column' => self::HASH_COLUMN,
        ]);
    }

    /**
     * Teardown
     */
    public function tearDown() : void
    {
        global $wpdb;
        $wpdb->query( 'DROP TEMPORARY TABLE ' . $this->get_hash_table_name() );
        parent::tearDown();
    }

    /**
     * TEST: Can create temporary table
     */
    /* public function testCanCreateTempTable() : void
    {
        self::commit_transaction();
        $this->checker->start_transaction();

        $table = $this->db->get_results(
            "SHOW COLUMNS FROM {$this->get_hash_table_name()};"
        );

        $this->assertNotEmpty( $table );
    } */

    /**
     * TEST: Temporary table has correct records
     * 
     * @depends testCanCreateTempTable
     */
    /* public function testTemporaryTableHasCorrectRecords() : void
    {
        $this->hash_map->method('get_map')->willReturn([
            'item_1' => '11111',
            'item_2' => '22222',
        ]);
        self::commit_transaction();
        $this->checker->start_transaction();

        $results = $this->db->get_results(
            "SELECT * FROM " . $this->get_hash_table_name(),
            ARRAY_A
        );

        $this->assertIsArray( $results );
        $this->assertCount( 2, $results );
        $this->assertEquals( '22222', $results[1]['hash_value'] );
    } */

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Get hash table name
     */
    private function get_hash_table_name() : string
    {
        return sanitize_key( $this->db->prefix . self::DB_TABLE . '_comparison_hash_map' );
    }

}   // End of class