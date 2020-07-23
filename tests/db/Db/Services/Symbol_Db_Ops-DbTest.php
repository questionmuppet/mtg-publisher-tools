<?php
declare(strict_types=1);

use Mtgtools\Db\Services\Symbol_Db_Ops;
use Mtgtools\Updates\Db_Update_Checker;
use Mtgtools\Symbols\Mana_Symbol;
use Mtgtools\Exceptions\Db as Exceptions;

class Symbol_Db_Ops_DbTest extends Mtgtools_UnitTestCase
{
    /**
     * Dummy table name
     */
    const TEST_TABLE = 'mtgtools_symbols_TEST';

    /**
     * Symbol_Db_Ops object
     */
    private $db_ops;

    /**
     * Setup
     */
    public function setUp() : void
    {
        parent::setUp();
        global $wpdb;
        $this->db_ops = new Symbol_Db_Ops( $wpdb, [
            'table' => self::TEST_TABLE
        ]);
    }

    /**
     * Teardown
     */
    public function tearDown() : void
    {
        global $wpdb;
        $wpdb->query(
            sprintf(
                "DROP TABLE IF EXISTS %s",
                sanitize_key( $wpdb->prefix . self::TEST_TABLE )
            )
        );
        parent::tearDown();
    }

    /**
     * -------------
     *   T E S T S
     * -------------
     */

    /**
     * TEST: Can create table
     */
    public function testCanCreateTable() : void
    {
        $result = $this->db_ops->create_table();
        $this->assertTrue( $result );
    }

    /**
     * TEST: Can drop table
     */
    public function testCanDropTable() : void
    {
        $result = $this->db_ops->drop_table();
        $this->assertTrue( $result );
    }

    /**
     * TEST: Can add valid symbol
     * 
     * @depends testCanCreateTable
     */
    public function testCanAddValidSymbol() : void
    {
        $symbol = $this->get_mock_symbol();
        $this->db_ops->create_table();
        
        $result = $this->db_ops->add_symbol( $symbol );
        
        $this->assertTrue( $result );
    }

    /**
     * TEST: Cannnot add invalid symbol
     * 
     * @depends testCanAddValidSymbol
     */
    public function testAddingInvalidSymbolThrowsDbException() : void
    {
        $symbol = $this->get_mock_symbol([ 'is_valid' => false ]);
        $this->db_ops->create_table();

        $this->expectException( Exceptions\DbException::class );

        $this->db_ops->add_symbol( $symbol );
    }

    /**
     * TEST: Can update extant symbol
     * 
     * @depends testCanAddValidSymbol
     */
    public function testCanUpdateExtantSymbol() : void
    {
        $symbol = $this->get_mock_symbol();
        $this->db_ops->create_table();
        $this->db_ops->add_symbol( $symbol );

        $symbol_2 = $this->get_mock_symbol([ 'english_phrase' => 'A nice, new phrase.' ]);
        $result = $this->db_ops->add_symbol( $symbol_2 );

        $this->assertTrue( $result );
    }
    
    /**
     * TEST: Can delete symbol
     * 
     * @depends testCanAddValidSymbol
     */
    public function testCanDeleteSymbol() : void
    {
        $symbol = $this->get_mock_symbol();
        $this->db_ops->create_table();
        $this->db_ops->add_symbol( $symbol );
        
        $result = $this->db_ops->delete_symbol( $symbol->get_plaintext() );
        
        $this->assertTrue( $result );
    }

    /**
     * TEST: Can get all mana symbols
     * 
     * @depends testCanAddValidSymbol
     */
    public function testCanGetAllManaSymbols() : void
    {
        $this->db_ops->create_table();
        foreach ( $this->get_mock_symbols(3) as $symbol )
        {
            $this->db_ops->add_symbol( $symbol );
        }

        $result = $this->db_ops->get_mana_symbols();

        $this->assertCount( 3, $result );
        $this->assertContainsOnlyInstancesOf( Mana_Symbol::class, $result );
    }

    /**
     * TEST: Can get mana symbols with filter
     * 
     * @depends testCanGetAllManaSymbols
     */
    public function testCanGetManaSymbolsWithFilter() : void
    {
        $this->db_ops->create_table();
        foreach ( $this->get_mock_symbols(10) as $symbol )
        {
            $this->db_ops->add_symbol( $symbol );
        }

        $result = $this->db_ops->get_mana_symbols([ 'plaintext' => 'U' ]);

        $this->assertCount( 3, $result );
    }

    /**
     * TEST: Invalid query filter throws DbException
     * 
     * @depends testCanGetManaSymbolsWithFilter
     */
    public function testInvalidQueryFilterThrowsDbException() : void
    {
        $this->db_ops->create_table();

        $this->expectException( Exceptions\DbException::class );
        
        $this->db_ops->get_mana_symbols([ 'invalid_param' => 'Uh, oh! Better put the kibbosh on it!' ]);
    }

    /**
     * -------------------------------
     *   U P D A T E   C H E C K E R
     * -------------------------------
     */

    /**
     * TEST: Can get update checker
     */
    public function testCanGetUpdateChecker() : void
    {
        $symbols = $this->get_mock_symbols(2);

        $checker = $this->db_ops->get_update_checker( $symbols );

        $this->assertInstanceOf( Db_Update_Checker::class, $checker );
    }

}   // End of class