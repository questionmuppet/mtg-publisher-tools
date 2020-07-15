<?php
/**
 * Card_Db_Ops
 * 
 * Handles database operations for Magic cards
 */

namespace Mtgtools\Cards;

use Mtgtools\Abstracts\Db_Ops;
use Mtgtools\Exceptions\Db as Exceptions;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Card_Db_Ops extends Db_Ops
{
    /**
     * Db tables
     */
    protected $tables = [
        'cards' => 'mtgtools_cards',
        'images' => 'mtgtools_images',
    ];

    /**
     * Valid filter arguments for query
     */
    protected $valid_filters = [ 'card_uuid', 'type', 'uuid', 'name', 'set_code', 'language', 'collector_number' ];

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
     * Find a card and all cached image uris
     * 
     * @param array $filters One or more "column" => "value" pairs to search by
     */
    public function find_card( array $filters ) : Magic_Card
    {
        if ( !count( $filters ) )
        {
            throw new \DomainException( get_called_class() . " tried to search for a card without any search criteria. You must include at least one filter." );
        }
        return $this->create_card( $this->get_card_row( $filters ) );
    }
    
    /**
     * Get card row matching filters
     */
    private function get_card_row( array $filters ) : array
    {
        $row = $this->db()->get_row(
            "SELECT uuid, name, set_code, language, collector_number, images
            FROM {$this->get_cards_table()}
            LEFT JOIN (
                SELECT card_uuid, JSON_ARRAYAGG(
                    JSON_OBJECT('card_uuid', card_uuid, 'type', type, 'uri', uri, 'cached', cached)
                ) AS images
                FROM {$this->get_images_table()}
                GROUP BY card_uuid
            ) aggregate_images ON card_uuid = uuid
            WHERE {$this->where_conditions( $filters )};",
            ARRAY_A
        );
        if ( is_null( $row ) )
        {
            throw new Exceptions\DbException( "No card record was found in the database matching the provided filters." );
        }
        return $row;
    }

    /**
     * -------------------------
     *   M A G I C   C A R D S
     * -------------------------
     */

    /**
     * Create card object
     */
    private function create_card( array $data ) : Magic_Card
    {
        $data['images'] = empty( $data['images'] )
            ? []
            : $this->create_images_from_json( $data['images'] );
        return new Magic_Card( $data );
    }

    /**
     * Create image objects from json
     * 
     * @return Image_Uri[]
     */
    private function create_images_from_json( string $json ) : array
    {
        $images = [];
        foreach ( json_decode( $json, true ) as $data )
        {
            $data['cache_period'] = $this->get_cache_period();
            $image = new Image_Uri( $data );
            $images[ $image->get_type() ] = $image;
        }
        return $images;
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
        $this->save_record([
            'table' => 'cards',
            'identifiers' => [ 'uuid' ],
            'values' => [
                'uuid' => $card->get_uuid(),
                'name' => $card->get_name(),
                'set_code' => $card->get_set_code(),
                'language' => $card->get_language(),
                'collector_number' => $card->get_collector_number(),
            ],
        ]);
        $this->cache_image_uris( $card->get_images(), $img_type );
    }
    
    /**
     * Update image uris and reset timestamp
     */
    private function cache_image_uris( array $images, string $type ) : void
    {
        array_key_exists( $type, $images )
            ? $this->update_image( $images[ $type ] )
            : $this->update_multiple_images( $images );
    }

    /**
     * Update multiple image uris
     */
    private function update_multiple_images( array $images )
    {
        foreach ( $images as $image )
        {
            $this->update_image( $image );
        }
    }

    /**
     * Update image uri
     */
    private function update_image( Image_Uri $image ) : void
    {
        $this->save_record([
            'table' => 'images',
            'identifiers' => [ 'card_uuid', 'type' ],
            'values' => [
                'card_uuid' => $image->get_card_uuid(),
                'type' => $image->get_type(),
                'uri' => $image->get_uri(),

                /**
                 * This is a bug. It will insert current server time
                 * rather than current db time. Should be unescaped
                 * "now()" in query instead.
                 */
                'cached' => date( 'Y-m-d H:i:s' ),
            ],
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
        $this->start_transaction();
        $result = $this->create_cards_table() && $this->create_images_table();
        $result
            ? $this->commit_transaction()
            : $this->rollback_transaction();
        return $result;
    }
    
    /**
     * Create cards table
     */
    private function create_cards_table() : bool
    {
        return $this->db()->query(
            "CREATE TABLE IF NOT EXISTS {$this->get_cards_table()} (
                id int(20) UNSIGNED AUTO_INCREMENT,
                uuid varchar(128) UNIQUE NOT NULL,
                name text NOT NULL,
                set_code varchar(16) NOT NULL,
                collector_number varchar(16) NOT NULL,
                language varchar(16) NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY printing (set_code, collector_number, language)
            ) {$this->get_collate()};"
        );
    }

    /**
     * Create images table
     */
    private function create_images_table() : bool
    {
        return $this->db()->query(
            "CREATE TABLE IF NOT EXISTS {$this->get_images_table()} (
                id int(20) UNSIGNED AUTO_INCREMENT,
                card_uuid varchar(128) NOT NULL,
                type varchar(16) NOT NULL,
                uri text NOT NULL,
                cached timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                FOREIGN KEY (card_uuid)
                    REFERENCES {$this->get_cards_table()}(uuid)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                UNIQUE KEY image_address (card_uuid, type)
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
        return $this->db()->query( "DROP TABLE IF EXISTS {$this->get_images_table()};" )
            && $this->db()->query( "DROP TABLE IF EXISTS {$this->get_cards_table()};");
    }

    /**
     * Get cards table sanitized for SQL statement
     */
    private function get_cards_table() : string
    {
        return sanitize_key( $this->get_table_name( 'cards' ) );
    }

    /**
     * Get images table sanitized for SQL statement
     */
    private function get_images_table() : string
    {
        return sanitize_key( $this->get_table_name( 'images' ) );
    }

}   // End of class