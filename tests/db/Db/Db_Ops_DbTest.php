<?php
declare(strict_types=1);

use Mtgtools\Tests\TestCases\Mtgtools_UnitTestCase;
use Mtgtools\Db\Db_Ops;
use Mtgtools\Db\Sql_Tokens\Sql_Token;
use Mtgtools\Exceptions\Db as Exceptions;

/**
 * Dummy implementation
 */
class Db_Ops_Nonabstract extends Db_Ops
{
    public function execute( $query )   { return $this->execute_query( $query ); }
    public function start()             { return $this->start_transaction(); }
    public function commit()            { return $this->commit_transaction(); }
    public function rollback()          { return $this->rollback_transaction(); }
    public function token( $c, $t )     { return $this->get_whitelisted_token( $c, $t ); }
    public function strip( $key )       { return $this->strip_backticks( $key ); }
    public function collate()           { return $this->get_collate(); }
}

class Db_Ops_DbTest extends Mtgtools_UnitTestCase
{
    /**
     * Constants
     */
    const TABLE = 'test_table';
    const NARF = [
        'unique_identifier' => 'narf',
    ];
    const TOKEN_CONTEXT = 'sensitive_and_dangerous';

    /**
     * SUT object
     */
    private $db_ops;

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
        $this->db_ops = $this->create_db_ops();
    }

    /**
     * Teardown
     */
    public function tearDown() : void
    {
        $this->drop_table();
        $this->wpdb->suppress_errors( false );
        $this->db_ops = null;
        parent::tearDown();
    }

    /**
     * -------------------------
     *   B A S I C   T E S T S
     * -------------------------
     */

    /**
     * TEST: Can get charset collation
     */
    public function testCanGetCollate() : void
    {
        $collate = $this->db_ops->collate();

        $this->assertIsString( $collate );
    }

    /**
     * ---------------------------
     *   S A N I T I Z A T I O N
     * ---------------------------
     */

    /**
     * TEST: Can strip backticks
     */
    public function testCanStripBackticks() : void
    {
        $clean = $this->db_ops->strip( 'column_with_`````backticks' );

        $this->assertEquals( 'column_with_backticks', $clean );
    }

    /**
     * TEST: Can get whitelisted token string
     */
    public function testCanGetWhitelistedTokenString() : void
    {
        $token = $this->createMock( Sql_Token::class );
        $token->method('is_safe_for')
            ->with( $this->equalTo( self::TOKEN_CONTEXT ) )
            ->willReturn( true );

        $snippet = $this->db_ops->token( self::TOKEN_CONTEXT, $token );

        $this->assertIsString( $snippet );
    }

    /**
     * TEST: Invalid whitelisted token throws exception
     * 
     * @depends testCanGetWhitelistedTokenString
     */
    public function testInvalidWhitelistedTokenThrowsException() : void
    {
        $token = $this->createMock( Sql_Token::class );
        $token->method('is_safe_for')
            ->with( $this->equalTo( self::TOKEN_CONTEXT ) )
            ->willReturn( false );
        
        $this->expectException( DomainException::class );

        $snippet = $this->db_ops->token( self::TOKEN_CONTEXT, $token );
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
        $this->db_ops->start();
        $this->insert_record( self::NARF );
        $this->db_ops->commit();

        $this->assertIsArray( $this->get_record('narf') );
    }

    /**
     * TEST: Can rollback a transaction
     * 
     * @depends testCanCommitTransaction
     */
    public function testCanRollbackTransaction() : void
    {
        $this->db_ops->start();
        $this->insert_record( self::NARF );
        $this->db_ops->rollback();

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
        $rows = $this->db_ops->execute( "INSERT INTO {$this->get_table()} (unique_identifier) VALUES ('narf') ;" );

        $record = $this->get_record('narf');
        $this->assertEquals( 1, $rows, 'Failed to assert that execute_query() returned the number of rows affected.' );
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
        $this->db_ops->execute( "SELECT * FROM {$this->get_table()} WHERE invalid_column = 'narf';" );
    }

    /**
     * TEST: Can get rows affected by query
     * 
     * @depends testCanExecuteGenericQuery
     */
    public function testCanGetRowsAffectedByQuery() : void
    {
        $this->db_ops->execute( "INSERT INTO {$this->get_table()} (unique_identifier) VALUES ('narf') ;" );

        $rows = $this->db_ops->get_rows_affected();

        $this->assertEquals( 1, $rows );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */

    /**
     * Create db ops object
     */
    private function create_db_ops() : Db_Ops
    {
        return new Db_Ops_Nonabstract( $this->wpdb );
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
                PRIMARY KEY (id)
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