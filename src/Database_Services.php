<?php
/**
 * Database_Services
 * 
 * Bootstraps classes for installing and querying custom db tables
 */

namespace Mtgtools;

use Mtgtools\Db\Services;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Database_Services
{
    /**
     * Services
     */
    private $symbols;
    private $cards;

    /**
     * Dependencies
     */
    private $wpdb;

    /**
     * Constructor
     */
    public function __construct( \wpdb $wpdb )
    {
        $this->wpdb = $wpdb;
    }

    /**
     * ---------------------------
     *   I N S T A L L A T I O N
     * ---------------------------
     */

    /**
     * Install custom tables
     */
    public function install() : void
    {
        $this->symbols()->create_table();
        $this->cards()->create_tables();
    }

    /**
     * Uninstall custom tables
     */
    public function uninstall() : void
    {
        $this->symbols()->drop_table();
        $this->cards()->drop_tables();
    }

    /**
     * -------------------
     *   S E R V I C E S
     * -------------------
     */

    /**
     * Get mana-symbols service
     */
    public function symbols() : Services\Symbol_Db_Ops
    {
        if ( !isset( $this->symbols ) )
        {
            $this->symbols = new Services\Symbol_Db_Ops( $this->wpdb );
        }
        return $this->symbols;
    }

    /**
     * Get cards service
     */
    public function cards() : Services\Card_Db_Ops
    {
        if ( !isset( $this->cards ) )
        {
            $this->cards = new Services\Card_Db_Ops( $this->wpdb );
        }
        return $this->cards;
    }

}   // End of class