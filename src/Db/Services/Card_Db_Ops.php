<?php
/**
 * Card_Db_Ops
 * 
 * Handles database operations for Magic cards
 */

namespace Mtgtools\Db\Services;

use Mtgtools\Cards\Magic_Card;
use Mtgtools\Cards\Image_Uri;

use Mtgtools\Db\Db_Ops;
use Mtgtools\Db\Db_Table;
use Mtgtools\Db\Sql_Tokens\Sql_Current_Timestamp;
use Mtgtools\Exceptions\Db as Exceptions;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Card_Db_Ops extends Db_Ops
{
    /**
     * Db tables
     */
    protected $tables = [
        'cards' => null,
        'images' => null,
    ];

    /**
     * Image cache period
     */
    private $cache_period = 0;
    
    /**
     * -------------
     *   Q U E R Y
     * -------------
     */

    /**
     * Find a card and corresponding image uris
     * 
     * @param array $filters    One or more column-value pairs to search by
     * @return Magic_Card       First card matching filters, with all cached image uris
     * @throws NoResultsException
     */
    public function find_card( array $filters ) : Magic_Card
    {
        $card = $this->cards()->get_record( $filters );
        $images = $this->images()->find_records([
            'filters' => [
                'card_id' => $card['id']
            ]
        ]);
        $card['images'] = $this->create_images( $images );
        return new Magic_Card( $card );
    }

    /**
     * Create all images
     */
    private function create_images( array $records ) : array
    {
        $images = [];
        foreach ( $records as $data )
        {
            $image = $this->create_image( $data );
            $images[ $image->get_type() ] = $image;
        }
        return $images;
    }
    
    /**
     * Create image from db data
     */
    private function create_image( array $data ) : Image_Uri
    {
        $data['cache_period'] = $this->get_cache_period();
        return new Image_Uri( $data );
    }
    
    /**
     * ---------------------
     *   S A V E   D A T A
     * ---------------------
     */

    /**
     * Update card data and cache image uris
     * 
     * @param Magic_Card $card  Card to save or update
     * @param string $img_type  Type of image to cache, omit for all
     */
    public function cache_card_data( Magic_Card $card, string $img_type = '' ) : void
    {
        $id = $this->cards()->save_record([
            'uuid' => $card->get_uuid(),
            'backface' => $card->is_backface(),
            'name' => $card->get_name(),
            'set_code' => $card->get_set_code(),
            'set_name' => $card->get_set_name(),
            'language' => $card->get_language(),
            'collector_number' => $card->get_collector_number(),
        ]);
        $this->cache_image_uris( $card->get_images(), $img_type, $id );
    }
    
    /**
     * Update image uris and reset timestamp
     */
    private function cache_image_uris( array $images, string $type, int $card_id ) : void
    {
        $to_cache = array_key_exists( $type, $images )
            ? array( $images[ $type ] )
            : $images;
        
        foreach ( $to_cache as $image )
        {
            $this->update_image( $image, $card_id );
        }
    }

    /**
     * Update image uri
     * 
     * @param Image_Uri $image      Instantiated image object
     * @param int $card_id          AUTO_INCREMENT id in cards table
     */
    private function update_image( Image_Uri $image, int $card_id ) : void
    {
        $this->images()->save_record([
            'card_id' => $card_id,
            'type' => $image->get_type(),
            'uri' => $image->get_uri(),
            'cached' => new Sql_Current_Timestamp(),
        ]);
    }

    /**
     * ---------------------------
     *   C A C H E   P E R I O D
     * ---------------------------
     */
    
    /**
     * Get image cache period
     * 
     * @return int Cache period in seconds
     */
    private function get_cache_period() : int
    {
        return $this->cache_period;
    }

    /**
     * Set image cache period
     */
    public function set_cache_period( int $seconds ) : void
    {
        $this->cache_period = $seconds;
    }

    /**
     * ---------------------------
     *   I N S T A L L A T I O N
     * ---------------------------
     */

    /**
     * Create custom tables in db
     * 
     * @return bool True if successful, false on error
     */
    public function create_tables() : bool
    {
        try
        {
            $this->start_transaction();
            $this->create_cards_table();
            $this->create_images_table();
            $this->commit_transaction();
            return true;
        }
        catch ( Exceptions\SqlErrorException $e )
        {
            $this->rollback_transaction();
            return false;
        }
    }
    
    /**
     * Create cards table
     */
    private function create_cards_table() : bool
    {
        return $this->execute_query(
            "CREATE TABLE IF NOT EXISTS {$this->get_cards_table_name()} (
                id INT UNSIGNED AUTO_INCREMENT,
                name TEXT NOT NULL,
                uuid VARCHAR(128) NOT NULL,
                backface BOOLEAN NOT NULL DEFAULT false,
                set_code VARCHAR(16) NOT NULL,
                set_name TINYTEXT NOT NULL,
                collector_number VARCHAR(16) NOT NULL,
                language VARCHAR(16) NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY unique_face (uuid, backface)
            ) {$this->get_collate()};"
        );
    }

    /**
     * Create images table
     */
    private function create_images_table() : bool
    {
        return $this->execute_query(
            "CREATE TABLE IF NOT EXISTS {$this->get_images_table_name()} (
                id INT UNSIGNED AUTO_INCREMENT,
                card_id INT UNSIGNED NOT NULL,
                type VARCHAR(16) NOT NULL,
                uri TEXT NOT NULL,
                cached TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                FOREIGN KEY (card_id)
                    REFERENCES {$this->get_cards_table_name()}(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                UNIQUE KEY image_address (card_id, type)
            ) {$this->get_collate()};"
        );
    }

    /**
     * Remove tables from db
     * 
     * @return bool True if successful, false on error
     */
    public function drop_tables() : bool
    {
        try
        {
            $this->start_transaction();
            $this->execute_query( "DROP TABLE IF EXISTS {$this->get_images_table_name()};" );
            $this->execute_query( "DROP TABLE IF EXISTS {$this->get_cards_table_name()};" );
            $this->commit_transaction();
            return true;
        }
        catch ( Exceptions\SqlErrorException $e )
        {
            $this->rollback_transaction();
            return false;
        }
    }

    /**
     * Get cards table sanitized for SQL statement
     */
    private function get_cards_table_name() : string
    {
        return $this->cards()->get_table_name();
    }

    /**
     * Get images table sanitized for SQL statement
     */
    private function get_images_table_name() : string
    {
        return $this->images()->get_table_name();
    }

    /**
     * ---------------
     *   T A B L E S
     * ---------------
     */

    /**
     * Get cards db table
     */
    private function cards() : Db_Table
    {
        if ( !isset( $this->tables['cards'] ) )
        {
            $this->tables['cards'] = new Db_Table( $this->db(), [
                'table' => 'mtgtools_cards',
                'filters' => [
                    'uuid',
                    'backface',
                    'name',
                    'set_code',
                    'collector_number',
                    'language',
                ],
                'field_types' => [
                    'backface' => '%d',
                ],
            ]);
        }
        return $this->tables['cards'];
    }

    /**
     * Get images db table
     */
    private function images() : Db_Table
    {
        if ( !isset( $this->tables['images'] ) )
        {
            $this->tables['images'] = new Db_Table( $this->db(), [
                'table' => 'mtgtools_images',
                'filters' => [
                    'card_id',
                    'type',
                ],
                'field_types' => [],
            ]);
        }
        return $this->tables['images'];
    }

}   // End of class