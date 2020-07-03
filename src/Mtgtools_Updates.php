<?php
/**
 * Mtgtools_Updates
 * 
 * Tracks and installs updates from an MTG data source
 */

namespace Mtgtools;
use Mtgtools\Abstracts\Module;
use Mtgtools\Symbols\Symbol_Db_Ops;
use Mtgtools\Interfaces\Mtg_Data_Source;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Updates extends Module
{
    /**
     * Symbol database ops
     */
    private $db_ops;
    
    /**
     * MTG data source
     */
    private $source;

    /**
     * Constructor
     */
    public function __construct( Symbol_Db_Ops $db_ops, Mtg_Data_Source $source, $wp_tasks )
    {
        $this->db_ops = $db_ops;
        $this->source = $source;
        parent::__construct( $wp_tasks );
    }

    /**
     * Add WP hooks
     */
    public function add_hooks() : void
    {
        add_action( 'mtgtools_dashboard_tabs', array( $this, 'add_dash_tab' ), 5, 1 );
        add_action( 'admin_notices', array( $this, 'print_notices' ) );
    }

    /**
     * ---------------------
     *   D A S H B O A R D
     * ---------------------
     */

    /**
     * Create dashboard tab
     */
    public function add_dash_tab( Mtgtools_Dashboard $dashboard ) : void
    {
        $dashboard->add_tab([
            'id' => 'updates',
            'title' => 'Updates',
        ]);
    }

    /**
     * -----------------
     *   U P D A T E S
     * -----------------
     */

    /**
     * Print admin notice if updates are available
     * 
     * @hooked admin_notices
     */
    public function print_notices() : void
    {
        if ( $this->updates_available() )
        {
            $message = $this->get_template_markup([
                'path' => 'dashboard/notices/updates-available.php',
                'themeable' => false,
            ]);
            $this->print_admin_notice([
                'title'   => 'Mana Symbol Updates Available',
                'type'    => 'info',
                'message' => $message,
                'p_wrap'  => false,
            ]);
        }
    }

    /**
     * Check for available updates
     */
    private function updates_available() : bool
    {
        return true;
    }

    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * Get db ops
     */
    private function db_ops() : Symbol_Db_Ops
    {
        return $this->db_ops;
    }

    /**
     * Get MTG data source
     */
    private function source() : Mtg_Data_Source
    {
        return $this->source;
    }

}   // End of class