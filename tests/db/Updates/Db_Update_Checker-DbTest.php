<?php
declare(strict_types=1);

use Mtgtools\Updates\Db_Update_Checker;
use Mtgtools\Interfaces\Hash_Map;

class Db_Update_Checker_DbTest extends Mtgtools_UnitTestCase
{
    /**
     * Db table keys
     */
    const DB_TABLE = 'mtgtools_comparison_TEST';
    const KEY_COLUMN = 'test_id';
    const HASH_COLUMN = 'test_hash';

    /**
     * Dummy records to compare
     */
    const DB_ELEMENTS = [
        'item_same' => '1xxxx',
        'item_changed' => '2xxxx',
        'item_deleted' => '3xxxx',
    ];
    const HASH_ELEMENTS = [
        'item_same' => '1xxxx',
        'item_changed' => '22222',
        'item_new' => '4xxxx',
    ];

    /**
     * Dependencies
     */
    private $wpdb;
    private $hash_map;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        global $wpdb;
        $wpdb->query( 'ROLLBACK;' );    // Stop the transaction initiated in WP TestCase

        $this->wpdb = $wpdb;
        $this->hash_map = $this->createMock( Hash_Map::class );
    }

    /**
     * Teardown
     */
    public function tearDown() : void
    {
        $this->wpdb->query( 'DROP TABLE IF EXISTS ' . $this->get_dummy_table() ); 
        $this->wpdb->query( 'DROP TABLE IF EXISTS ' . $this->get_hash_table() );
        parent::tearDown();
    }

    /**
     * ---------------------
     *   E X E C U T I O N
     * ---------------------
     */

    /**
     * TEST: Can execute db comparison
     */
    public function testCanExecuteComparison() : Db_Update_Checker
    {
        $this->create_dummy_table();
        $this->hash_map->method('get_map')->willReturn( self::HASH_ELEMENTS );
        $checker = $this->create_checker();

        $records = $checker->records_to_add();

        $this->assertIsArray( $records );

        return $checker;
    }

    /**
     * -----------------------
     *   C O M P A R I S O N
     * -----------------------
     */
    
    /**
     * TEST: Can get add records
     * 
     * @depends testCanExecuteComparison
     */
    public function testCanGetAddRecords( Db_Update_Checker $checker ) : void
    {
        $to_add = $checker->records_to_add();

        $this->assertCount( 1, $to_add );
        $this->assertContains( 'item_new', $to_add, 'Records marked for addition did not contain an expected item.' );
    }

    /**
     * TEST: Can get update records
     * 
     * @depends testCanExecuteComparison
     */
    public function testCanGetUpdateRecords( Db_Update_Checker $checker ) : void
    {
        $to_update = $checker->records_to_update();

        $this->assertCount( 1, $to_update );
        $this->assertContains( 'item_changed', $to_update, 'Records marked for update did not contain an expected item.' );
    }

    /**
     * TEST: Can get delete records
     * 
     * @depends testCanExecuteComparison
     */
    public function testCanGetDeleteRecords( Db_Update_Checker $checker ) : void
    {
        $to_delete = $checker->records_to_delete();

        $this->assertCount( 1, $to_delete );
        $this->assertContains( 'item_deleted', $to_delete, 'Records marked for deletion did not contain an expected item.' );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Create update checker
     */
    private function create_checker() : Db_Update_Checker
    {
        return new Db_Update_Checker( $this->wpdb, $this->hash_map, [
            'db_table' => self::DB_TABLE,
            'key_column' => self::KEY_COLUMN,
            'hash_column' => self::HASH_COLUMN,
        ]);
    }

    /**
     * Create comparison test table
     */
    private function create_dummy_table() : void
    {
        $this->wpdb->query(
            sprintf(
                "CREATE TABLE %s (
                    id int(10) UNSIGNED AUTO_INCREMENT,
                    %s varchar(256) UNIQUE NOT NULL,
                    %s text NOT NULL,
                    PRIMARY KEY (id)
                );",
                $this->get_dummy_table(),
                self::KEY_COLUMN,
                self::HASH_COLUMN
            )
        );
        $this->insert_dummy_rows();
    }

    /**
     * Insert dummy rows into comparison table
     */
    private function insert_dummy_rows() : void
    {
        foreach( self::DB_ELEMENTS as $key => $hash )
        {
            $this->wpdb->insert(
                $this->get_dummy_table(),
                [
                    self::KEY_COLUMN => $key,
                    self::HASH_COLUMN => $hash,
                ]
            );
        }
    }

    /**
     * Get hash table keyname
     */
    private function get_hash_table() : string
    {
        return sanitize_key( $this->get_dummy_table() . '_comparison_hash_map' );
    }

    /**
     * Get dummy table keyname
     */
    private function get_dummy_table() : string
    {
        return sanitize_key( $this->wpdb->prefix . self::DB_TABLE );
    }

}   // End of class