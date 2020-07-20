<?php
declare(strict_types=1);

use Mtgtools\Db\Db_Table;
use Mtgtools\Exceptions\Db as Exceptions;

class Db_Table_DbTest extends Mtgtools_UnitTestCase
{
    /**
     * Constants
     */
    const TABLE = 'TEST_table_name';
    const FILTERS = [
        'unique_identifier',
        'search_term_1',
        'search_term_2',
    ];
    const FIELD_TYPES = [
        'integer_attribute' => '%d',
        'boolean_attribute' => '%d',
    ];

    /**
     * Dummy records
     */
    const NARF = [
        'unique_identifier' => 'narf',
        'search_term_1' => 'one',
    ];
    const ZORT = [
        'unique_identifier' => 'zort',
        'search_term_1' => 'one',
        'search_term_2' => 'two',
    ];
    const POIT = [
        'unique_identifier' => 'poit',
        'integer_attribute' => 42,
        'string_attribute' => 'a healthy string',
    ];

    /**
     * SUT object
     */
    private $db_table;

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
        $this->create_table();
        $this->db_table = $this->create_db_table();
    }

    /**
     * Teardown
     */
    public function tearDown() : void
    {
        $this->drop_table();
        $this->wpdb->suppress_errors( false );
        $this->db_table = null;
        parent::tearDown();
    }

    /**
     * ---------------
     *   T A B L E S
     * ---------------
     */

    /**
     * TEST: Can set table parameters
     */
    public function testCanSetTableParameters() : void
    {
        $result = $this->db_table->set_table_props([
            'table' => self::TABLE,
            'filters' => self::FILTERS,
            'field_types' => self::FIELD_TYPES,
        ]);

        $this->assertNull( $result );
    }

    /**
     * TEST: Can get charset collation
     */
    public function testCanGetCollate() : void
    {
        $collate = $this->db_table->get_collate();

        $this->assertIsString( $collate );
    }

    /**
     * TEST: Can get table name
     * 
     * @depends testCanSetTableParameters
     */
    public function testCanGetTableName() : void
    {
        $table = $this->db_table->get_table_name();

        $this->assertEquals( $this->wpdb->prefix . self::TABLE, $table );
    }

    /**
     * TEST: Requesting undefined table name throws exception
     * 
     * @depends testCanGetTableName
     */
    public function testRequestingUndefinedTableNameThrowsException() : void
    {
        $db_table = new Db_Table( $this->wpdb );

        $this->expectException( OutOfRangeException::class );

        $db_table->get_table_name();
    }

    /**
     * -----------------
     *   C O L U M N S
     * -----------------
     */
    
    /**
     * TEST: Can get default column placeholder
     */
    public function testCanGetDefaultPlaceholder() : void
    {
        $ph = $this->db_table->get_column_placeholder( 'string_attribute' );

        $this->assertEquals( '%s', $ph, 'Failed to assert that an undefined column placeholder defaults to type string.' );
    }

    /**
     * TEST: Can get column placeholder from field_types
     * 
     * @depends testCanGetDefaultPlaceholder
     */
    public function testCanGetPlaceholderFromFieldTypes() : void
    {
        $ph = $this->db_table->get_column_placeholder( 'integer_attribute' );

        $this->assertEquals( '%d', $ph, 'Failed to assert that a column placeholder could be defined via field type.' );
    }

    /**
     * TEST: Can get format placeholders
     * 
     * @depends testCanGetPlaceholderFromFieldTypes
     */
    public function testCanGetFormatPlaceholders() : void
    {
        $phs = $this->db_table->get_format_placeholders([
            'unique_identifier' => 'narf',
            'string_attribute' => 'Test String',
        ]);

        $this->assertIsArray( $phs );
    }

    /**
     * ---------------------------
     *   S A N I T I Z A T I O N
     * ---------------------------
     */

    /**
     * TEST: Can sanitize array of column-value pairs
     * 
     * @depends testCanGetPlaceholderFromFieldTypes
     */
    public function testCanSanitizeColumnValuePairs() : void
    {
        $raw = [
            'column_with_`backtick' => "value with 'apostrophe",
        ];

        $sanitized = $this->db_table->sanitize_column_values( $raw );

        $col = array_key_first( $sanitized );
        $val = $sanitized[ $col ];
        $this->assertEquals( '`column_with_backtick`', $col, 'Failed to assert that a column keyname was sanitized for safe use in SQL queries.' );
        $this->assertEquals( "'value with \\'apostrophe'", $val, 'Failed to assert that a value was sanitized for safe use in SQL queries.' );
    }

    /**
     * ---------------------------
     *   T R A N S A C T I O N S
     * ---------------------------
     */

    /**
     * TEST: Can commit a transaction
     */
    public function testCanCommitTransaction() : void
    {
        $this->db_table->start_transaction();
        $this->insert_record( self::NARF );
        $this->db_table->commit_transaction();

        $this->assertIsArray( $this->get_record('narf') );
    }

    /**
     * TEST: Can rollback a transaction
     * 
     * @depends testCanCommitTransaction
     */
    public function testCanRollbackTransaction() : void
    {
        $this->db_table->start_transaction();
        $this->insert_record( self::NARF );
        $this->db_table->rollback_transaction();

        $this->assertNull( $this->get_record('narf') );
    }

    /**
     * -----------------------------
     *   G E N E R I C   Q U E R Y
     * -----------------------------
     */

    /**
     * TEST: Can execute generic query
     */
    public function testCanExecuteGenericQuery() : void
    {
        $this->db_table->execute_query( "INSERT INTO {$this->get_table()} (unique_identifier) VALUES ('narf') ;" );

        $record = $this->get_record('narf');
        $this->assertEquals( 'narf', $record['unique_identifier'] );
    }

    /**
     * TEST: Sql error in generic query throws exception
     * 
     * @depends testCanExecuteGenericQuery
     */
    public function testSqlErrorInGenericQueryThrowsException() : void
    {
        $this->expectException( Exceptions\SqlErrorException::class );

        $this->wpdb->suppress_errors();
        $this->db_table->execute_query( "SELECT * FROM {$this->get_table()} WHERE invalid_column = 'narf';" );
    }

    /**
     * -----------------
     *   F I L T E R S
     * -----------------
     */

    /**
     * TEST: Can generate exact conditional
     * 
     * @depends testCanGetPlaceholderFromFieldTypes
     */
    public function testCanGenerateExactConditional() : void
    {
        $expression = $this->db_table->prepare_conditional_expression( 'string_attribute', 'value' );

        $this->assertEquals( "`string_attribute` = 'value'", $expression );
    }

    /**
     * TEST: Can generate fuzzy conditional
     * 
     * @depends testCanGenerateExactConditional
     */
    public function testCanGenerateFuzzyConditional() : void
    {
        $expression = $this->db_table->prepare_conditional_expression( 'string_attribute', 'value', false );
        
        $this->assertRegExp( '/`string_attribute` LIKE \'{[a-f0-9]+}value{[a-f0-9]+}\'/', $expression );
    }

    /**
     * TEST: Can generate integer conditional
     * 
     * @depends testCanGenerateExactConditional
     */
    public function testCanGenerateIntegerConditional() : void
    {
        $expression = $this->db_table->prepare_conditional_expression( 'integer_attribute', 42, false );

        $this->assertEquals( "`integer_attribute` = 42", $expression );
    }

    /**
     * TEST: Can generate WHERE statement
     * 
     * @depends testCanGenerateExactConditional
     */
    public function testCanGenerateWhereStatement() : void
    {
        $where = $this->db_table->where_conditions([
            'search_term_1' => 'one',
            'search_term_2' => 'two',
        ]);

        $this->assertEquals( "`search_term_1` = 'one' && `search_term_2` = 'two'", $where );
    }

    /**
     * TEST: Invalid filter key throws exception
     * 
     * @depends testCanGenerateWhereStatement
     */
    public function testInvalidFilterInWhereConditionsThrowsException() : void
    {
        $this->expectException( DomainException::class );
        
        $this->db_table->where_conditions([
            'search_term_1' => 'one',
            'string_attribute' => 'invalid',
        ]);
    }

    /**
     * TEST: Can generate valid LIMIT statement
     */
    public function testCanGenerateValidLimitStatement() : void
    {
        $limit = $this->db_table->limit_statement( 10, 3 );

        $this->assertEquals( 'LIMIT 10 OFFSET 3', $limit );
    }

    /**
     * TEST: Invalid LIMIT statement resolves to empty string
     * 
     * @depends testCanGenerateValidLimitStatement
     */
    public function testInvalidLimitStatementResolvesToEmptyString() : void
    {
        $limit = $this->db_table->limit_statement( 0, 3 );

        $this->assertEquals( '', $limit );
    }

    /**
     * -----------------
     *   R E C O R D S
     * -----------------
     */

    /**
     * TEST: Record check returns true if exists
     * 
     * @depends testRequestingUndefinedTableNameThrowsException
     * @depends testInvalidFilterInWhereConditionsThrowsException
     */
    public function testRecordCheckReturnsTrueIfExists() : void
    {
        $this->insert_record( self::ZORT );

        $exists = $this->db_table->record_exists([
            'search_term_1' => self::ZORT['search_term_1'],
            'search_term_2' => self::ZORT['search_term_2'],
        ]);

        $this->assertTrue( $exists );
    }

    /**
     * TEST: Record check returns false if not exists
     * 
     * @depends testRecordCheckReturnsTrueIfExists
     */
    public function testRecordCheckReturnsFalseIfNotExists() : void
    {
        $exists = $this->db_table->record_exists([
            'search_term_1' => self::ZORT['search_term_1'],
            'search_term_2' => self::ZORT['search_term_2'],
        ]);

        $this->assertFalse( $exists );
    }

    /**
     * TEST: Can save new record
     * 
     * @depends testCanSanitizeColumnValuePairs
     * @depends testRequestingUndefinedTableNameThrowsException
     * @depends testSqlErrorInGenericQueryThrowsException
     */
    public function testCanSaveNewRecord() : void
    {
        $rows = $this->db_table->save_record( self::POIT );

        $row = $this->get_record('poit');

        $this->assertIsArray( $row );
        $this->assertEquals( self::POIT['string_attribute'], $row['string_attribute'] );
        $this->assertEquals( 1, $rows, 'Failed to assert that save_record() returned the number of rows inserted.' );
    }

    /**
     * TEST: Can update extant record
     * 
     * @depends testCanSaveNewRecord
     */
    public function testCanUpdateExtantRecord() : void
    {
        $this->db_table->save_record( self::POIT );

        $new = self::POIT;
        $new['string_attribute'] = 'An even healthier string';
        $new['search_term_1'] = 'entirely new information';

        $this->db_table->save_record( $new );

        $row = $this->get_record('poit');
        $this->assertEquals( 'An even healthier string', $row['string_attribute'] );
        $this->assertEquals( 'entirely new information', $row['search_term_1'] );
    }

    /**
     * TEST: Can find multiple records
     * 
     * @depends testRequestingUndefinedTableNameThrowsException
     * @depends testInvalidFilterInWhereConditionsThrowsException
     * @depends testInvalidLimitStatementResolvesToEmptyString
     * @depends testCanSaveNewRecord
     */
    public function testCanFindMultipleRecords() : void
    {
        $this->db_table->save_record( self::NARF );
        $this->db_table->save_record( self::ZORT );
        $this->db_table->save_record( self::POIT );

        $rows = $this->db_table->find_records([
            'filters' => [
                'search_term_1' => 'one'
            ]
        ]);

        $this->assertCount( 2, $rows );
    }

    /**
     * TEST: Finding records with no filters throws exception
     * 
     * @depends testCanFindMultipleRecords
     */
    public function testFindingRecordsWithNoFiltersThrowsException() : void
    {
        $this->expectException( DomainException::class );

        $this->db_table->find_records([]);
    }

    /**
     * TEST: Can find single record
     * 
     * @depends testCanFindMultipleRecords
     */
    public function testCanFindSingleRecord() : void
    {
        $this->db_table->save_record( self::NARF );
        $this->db_table->save_record( self::ZORT );
        $this->db_table->save_record( self::POIT );

        $record = $this->db_table->get_record([
            'search_term_1' => 'one'
        ]);

        $this->assertEquals( 'narf', $record['unique_identifier'] );
    }

    /**
     * TEST: No results from single-record search throws exception
     * 
     * @depends testCanFindSingleRecord
     */
    public function testNoResultsFromSingleRecordSearchThrowsException() : void
    {
        $this->db_table->save_record( self::NARF );
        $this->db_table->save_record( self::ZORT );
        $this->db_table->save_record( self::POIT );

        $this->expectException( Exceptions\NoResultsException::class );

        $this->db_table->get_record([
            'unique_identifier' => 'troz'
        ]);
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Create Db_Table object
     */
    private function create_db_table() : Db_Table
    {
        $props = [
            'table' => self::TABLE,
            'filters' => self::FILTERS,
            'field_types' => self::FIELD_TYPES,
        ];
        return new Db_Table( $this->wpdb, $props );
    }

    /**
     * Insert new dummy record
     * 
     * @see wpdb::insert()
     * @return mixed
     */
    private function insert_record( array $values )
    {
        return $this->wpdb->insert( $this->get_table(), $values );
    }

    /**
     * Get dummy record
     * 
     * @see wpdb::get_row()
     * @return mixed
     */
    private function get_record( string $key )
    {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->get_table()} WHERE unique_identifier = %s",
                $key
            ),
            ARRAY_A
        );
    }

    /**
     * Create test table
     */
    private function create_table() : void
    {
        $this->wpdb->query(
            "CREATE TABLE {$this->get_table()} (
                id SMALLINT UNSIGNED AUTO_INCREMENT,
                unique_identifier VARCHAR(128) UNIQUE NOT NULL,
                search_term_1 VARCHAR(128),
                search_term_2 VARCHAR(128),
                string_attribute TEXT,
                integer_attribute SMALLINT,
                boolean_attribute BOOLEAN,
                PRIMARY KEY (id),
                KEY (search_term_1),
                KEY (search_term_2)
            ) {$this->wpdb->get_charset_collate()};"
        );
    }

    /**
     * Drop test table
     */
    private function drop_table() : void
    {
        $this->wpdb->query( "DROP TABLE IF EXISTS {$this->get_table()};" );
    }

    /**
     * Get table name
     */
    private function get_table() : string
    {
        return $this->wpdb->prefix . self::TABLE;
    }

}   // End of class