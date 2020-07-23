<?php
declare(strict_types=1);

use Mtgtools\Db\Services\Card_Db_Ops;
use Mtgtools\Cards\Magic_Card;
use Mtgtools\Cards\Image_Uri;
use Mtgtools\Exceptions\Db as Exceptions;

class Card_Db_Ops_DbTest extends Mtgtools_UnitTestCase
{
    /**
     * Dummy table name
     */
    const TEST_TABLE = 'mtgtools_cards';
    const IMG_TEST_TABLE = 'mtgtools_images';

    /**
     * Mock Magic card attributes
     */
    const MOCK_CARD = [
        'uuid' => 'xxxxx',
        'backface' => false,
        'name' => 'Stoneforge Mystic',
        'set_code' => 'WWK',
        'set_name' => 'Worldwake',
        'language' => 'English',
        'collector_number' => '42a',
        'images' => [],
    ];

    /**
     * Mock image uri attributes
     */
    const IMAGE_1 = [
        'type' => 'small',
        'uri' => 'https://www.example.com/small.png',
    ];
    const IMAGE_2 = [
        'type' => 'large',
        'uri' => 'https://www.example.com/large.png',
    ];

    /**
     * Db_Ops object
     */
    private $db_ops;

    /**
     * Live dependencies
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
        $this->db_ops = new Card_Db_Ops( $this->wpdb );
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
     * Teardown
     */
    public function tearDown() : void
    {
        $tables = [
            self::IMG_TEST_TABLE,
            self::TEST_TABLE,
        ];
        foreach ( $tables as $table )
        {
            $this->wpdb->query(
                sprintf(
                    "DROP TABLE IF EXISTS %s",
                    sanitize_key( $this->wpdb->prefix . $table )
                )
            );
        }
        parent::tearDown();
    }

    /**
     * ---------------------------
     *   I N S T A L L A T I O N
     * ---------------------------
     */

    /**
     * TEST: Can create tables
     */
    public function testCanCreateTables() : void
    {
        $success = $this->db_ops->create_tables();

        $this->assertTrue( $success );
    }

    /**
     * TEST: Can drop tables
     * 
     * @depends testCanCreateTables
     */
    public function testCanDropTables() : void
    {
        $this->db_ops->create_tables();

        $success = $this->db_ops->drop_tables();

        $this->assertTrue( $success );
    }

    /**
     * -----------------------
     *   C A R D   C A C H E
     * -----------------------
     */

    /**
     * TEST: Can cache new Magic card
     * 
     * @depends testCanCreateTables
     * @return array Newly created row in db
     */
    public function testCanCacheNewMagicCard() : array
    {
        $this->db_ops->create_tables();
        $card = $this->get_mock_magic_card();

        $this->db_ops->cache_card_data( $card );

        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->get_cards_table()} WHERE uuid = %s",
                self::MOCK_CARD['uuid']
            ),
            ARRAY_A
        );

        $this->assertIsArray( $row );

        return $row;
    }
    
    /**
     * TEST: Card has correct attributes in db
     * 
     * @depends testCanCacheNewMagicCard
     */
    public function testNewlyInsertedCardHasCorrectAttributes( array $row ) : void
    {
        $this->assertEquals( self::MOCK_CARD['uuid'], $row['uuid'] );
        $this->assertEquals( self::MOCK_CARD['backface'], boolval( $row['backface'] ) );
        $this->assertEquals( self::MOCK_CARD['name'], $row['name'] );
        $this->assertEquals( self::MOCK_CARD['set_code'], $row['set_code'] );
        $this->assertEquals( self::MOCK_CARD['set_name'], $row['set_name'] );
        $this->assertEquals( self::MOCK_CARD['language'], $row['language'] );
        $this->assertEquals( self::MOCK_CARD['collector_number'], $row['collector_number'] );
    }

    /**
     * -------------------------
     *   I M A G E   C A C H E
     * -------------------------
     */

    /**
     * TEST: Can cache a single image uri
     * 
     * @depends testCanCacheNewMagicCard
     * @return array Newly created row in db
     */
    public function testCanCacheSingleImageUri() : array
    {
        $this->db_ops->create_tables();
        $card = $this->get_mock_magic_card([ 'images' => $this->get_mock_images() ]);

        $this->db_ops->cache_card_data( $card, 'small' );

        $images = $this->get_images_by_uuid( self::MOCK_CARD['uuid'] );
        $this->assertCount( 1, $images, 'Failed to assert that exactly one image uri was cached when a valid type was provided.' );

        return $images[0];
    }

    /**
     * TEST: New image uri has correct attributes
     * 
     * @depends testCanCacheSingleImageUri
     */
    public function testNewlyInsertedImageHasCorrectAttributes( array $row ) : void
    {
        $this->assertEquals( self::IMAGE_1['type'], $row['type'] );
        $this->assertEquals( self::IMAGE_1['uri'], $row['uri'] );
        $this->assertLessThanOrEqual( time(), $row['cached'], 'Failed to assert that the timestamp on a newly cached image uri is equal to or older than current time.' );
    }

    /**
     * TEST: Can update timestamp of an already cached uri
     * 
     * @depends testNewlyInsertedImageHasCorrectAttributes
     */
    public function testCanUpdateCachedUriWithNewTimestamp() : void
    {
        $this->db_ops->create_tables();
        $card = $this->get_mock_magic_card([ 'images' => $this->get_mock_images() ]);
        $this->db_ops->cache_card_data( $card, 'small' );

        $first = $this->get_cache_timestamp( self::MOCK_CARD['uuid'], 'small' );
        sleep(1);   // Wait 1 second
        $this->db_ops->cache_card_data( $card, 'small' );
        $second = $this->get_cache_timestamp( self::MOCK_CARD['uuid'], 'small' );

        $this->assertGreaterThan( $first, $second, 'Failed to assert that a newly cached uri updated the timestamp.' );
    }

    /**
     * TEST: Can cache all image uris
     * 
     * @depends testCanCacheSingleImageUri
     */
    public function testCanCacheAllImageUris() : void
    {
        $this->db_ops->create_tables();
        $card = $this->get_mock_magic_card([ 'images' => $this->get_mock_images() ]);

        $this->db_ops->cache_card_data( $card );

        $images = $this->get_images_by_uuid( self::MOCK_CARD['uuid'] );
        $this->assertCount( 2, $images );
    }

    /**
     * -------------
     *   Q U E R Y
     * -------------
     */

    /**
     * TEST: Query without filters throws DomainException
     */
    public function testQueryWithoutFiltersThrowsDomainException() : void
    {
        $this->expectException( \DomainException::class );

        $card = $this->db_ops->find_card([]);
    }

    /**
     * TEST: Can find card by uuid
     * 
     * @depends testNewlyInsertedImageHasCorrectAttributes
     */
    public function testCanFindCardByUuid() : void
    {
        $this->db_ops->create_tables();
        $this->db_ops->cache_card_data( $this->get_mock_magic_card() );

        $card = $this->db_ops->find_card([
            'uuid' => self::MOCK_CARD['uuid']
        ]);
        
        $this->assertInstanceOf( Magic_Card::class, $card );
    }

    /**
     * TEST: Can find card by composite key
     * 
     * @depends testCanFindCardByUuid
     */
    public function testCanFindCardByCompositeKey() : void
    {
        $this->db_ops->create_tables();
        $this->db_ops->cache_card_data( $this->get_mock_magic_card() );

        $card = $this->db_ops->find_card([
            'name' => self::MOCK_CARD['name'],
            'set_code' => self::MOCK_CARD['set_code'],
            'language' => self::MOCK_CARD['language'],
        ]);

        $this->assertInstanceOf( Magic_Card::class, $card );
    }

    /**
     * TEST: Zero-result search throws DbException
     * 
     * @depends testCanFindCardByCompositeKey
     */
    public function testZeroResultSearchThrowsDbException() : void
    {
        $this->db_ops->create_tables();
        $this->db_ops->cache_card_data( $this->get_mock_magic_card() );

        $this->expectException( Exceptions\DbException::class );

        $card = $this->db_ops->find_card([
            'name' => 'Incorrect Card Name',
        ]);
    }

    /**
     * TEST: Extant image data is added to Magic card
     * 
     * @depends testCanFindCardByUuid
     */
    public function testExtantImagesAddedToMagicCard() : void
    {
        $this->db_ops->create_tables();
        $this->db_ops->cache_card_data(
            $this->get_mock_magic_card([
                'images' => $this->get_mock_images()
            ])
        );

        $card = $this->db_ops->find_card([ 'uuid' => self::MOCK_CARD['uuid'] ]);

        $this->assertCount( 2, $card->get_images(), 'A Magic_Card returned by a query did not contain the expected number of image uris.' );
        $this->assertEquals( self::IMAGE_1['uri'], $card->get_image( self::IMAGE_1['type'] )->get_uri(), 'Failed to assert that an image uri returned by a query contained the expected values.' );
    }

    /**
     * ---------------------
     *   P R O V I D E R S
     * ---------------------
     */
    
    /**
     * Check timestamp of a cached image
     */
    private function get_cache_timestamp( string $uuid, string $type ) : string
    {
        return $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT cached FROM {$this->get_images_table()} i
                INNER JOIN {$this->get_cards_table()} c ON c.id = i.card_id
                WHERE uuid = %s && type = %s",
                $uuid,
                $type
            )
        );
    }

    /**
     * Get images matching a uuid
     */
    private function get_images_by_uuid( string $uuid ) : array
    {
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->get_images_table()} i
                INNER JOIN {$this->get_cards_table()} c ON c.id = i.card_id
                WHERE uuid = %s",
                $uuid
            ),
            ARRAY_A
        );
    }

    /**
     * Get mock Magic card with image uris
     */
    private function get_mock_magic_card( array $props = [] ) : Magic_Card
    {
        $props = array_replace( self::MOCK_CARD, $props );
        $card = $this->createMock( Magic_Card::class );
        foreach ( $props as $key => $prop )
        {
            $method = 'backface' === $key ? 'is_backface' : "get_{$key}";
            $card->method( $method )->willReturn( $prop );
        }
        return $card;
    }

    /**
     * Get mock images
     */
    private function get_mock_images() : array
    {
        return [
            self::IMAGE_1['type'] => $this->get_mock_image_uri( self::IMAGE_1 ),
            self::IMAGE_2['type'] => $this->get_mock_image_uri( self::IMAGE_2 ),
        ];
    }

    /**
     * Get mock image uri
     */
    private function get_mock_image_uri( array $props = [] ) : Image_Uri
    {
        $image = $this->createMock( Image_Uri::class );
        foreach ( $props as $key => $prop )
        {
            $image->method( "get_{$key}" )->willReturn( $prop );
        }
        return $image;
    }

    /**
     * Get sanitized cards table
     */
    private function get_cards_table() : string
    {
        return sanitize_key( $this->wpdb->prefix . self::TEST_TABLE );
    }

    /**
     * Get sanitized images table
     */
    private function get_images_table() : string
    {
        return sanitize_key( $this->wpdb->prefix . self::IMG_TEST_TABLE );
    }

}   // End of class