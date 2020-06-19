<?php
declare(strict_types=1);
use Mtgtools\Symbols\Symbol_Db_Ops;
use Mtgtools\Symbols\Mana_Symbol;
use Mtgtools\Exceptions\Db as Exceptions;

class Symbol_Db_OpsTest extends Mtgtools_UnitTestCase
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
     * Can create table
     */
    public function testCanCreateTable() : void
    {
        $result = $this->db_ops->create_table();
        $this->assertTrue( $result );
    }

    /**
     * Can drop table
     */
    public function testCanDropTable() : void
    {
        $result = $this->db_ops->drop_table();
        $this->assertTrue( $result );
    }

    /**
     * Can add valid symbol
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
     * Cannnot add invalid symbol
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
     * Cannot add duplicate symbol
     * 
     * @depends testCanAddValidSymbol
     */
    public function testAddingDuplicateSymbolThrowsDbException() : void
    {
        $symbol = $this->get_mock_symbol();
        $this->db_ops->create_table();
        $this->db_ops->add_symbol( $symbol );

        $this->expectException( Exceptions\DbException::class );

        $this->db_ops->add_symbol( $symbol );
    }
    
    /**
     * Can delete symbol
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
     * Can get all mana symbols
     * 
     * @depends testCanAddValidSymbol
     */
    public function testCanGetAllManaSymbols() : void
    {
        $this->db_ops->create_table();
        $symbol1 = $this->get_mock_symbol([ 'plaintext' => '{T}' ]);
        $symbol2 = $this->get_mock_symbol([ 'plaintext' => '{Q}' ]);
        $this->db_ops->add_symbol( $symbol1 );
        $this->db_ops->add_symbol( $symbol2 );

        $result = $this->db_ops->get_mana_symbols();

        $this->assertCount( 2, $result );
        $this->assertContainsOnlyInstancesOf( Mana_Symbol::class, $result );
    }

    /**
     * Can get a mana symbol by key
     * 
     * @depends testCanAddValidSymbol
     */
    public function testCanGetManaSymbolByKey() : void
    {
        $this->db_ops->create_table();
        $symbol = $this->get_mock_symbol();
        $this->db_ops->add_symbol( $symbol );

        $result = $this->db_ops->get_symbol( $symbol->get_plaintext() );

        $this->assertInstanceOf( Mana_Symbol::class, $result );
    }

    /**
     * Cannot get nonexistent mana symbol
     * 
     * @depends testCanAddValidSymbol
     */
    public function testGettingNonexistentSymbolThrowsOutOfRangeException() : void
    {
        $this->db_ops->create_table();
        $symbol = $this->get_mock_symbol([ 'plaintext' => '{T}' ]);
        $this->db_ops->add_symbol( $symbol );

        $this->expectException( \OutOfRangeException::class );

        $this->db_ops->get_symbol( '{FAKE}' );
    }

}   // End of class