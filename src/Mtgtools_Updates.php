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
        $this->register_post_handlers([
            [
                'type'         => 'redirect',
                'action'       => 'mtgtools_update_symbols',
                'callback'     => array( $this, 'update_symbols' ),
                'redirect_url' => $this->get_dashboard_url('updates'),
                'error_link'   => [
                    'url' => $this->get_dashboard_url('updates'),
                    'text' => 'Return to updates',
                ],
            ],
        ]);
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
     *   N O T I C E S
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
            $this->print_admin_notice([
                'title'   => 'Mana symbol updates available',
                'type'    => 'info',
                'message' => $this->get_update_message(),
                'buttons' => [
                    [
                        'label' => 'Update now',
                        'href' => $this->get_update_action_link(),
                    ],
                    [
                        'label' => 'Turn off notices',
                        'href' => '',
                    ],
                ],
            ]);
        }
    }

    /**
     * Get updates-available message
     */
    private function get_update_message() : string
    {
        return 'The MTG mana symbol database used by your posts and themes is out of date. To download the latest update and begin including the newest Magic content, click "Update now".';
    }

    /**
     * Get update action link
     */
    private function get_update_action_link() : string
    {
        return add_query_arg(
            [
                'action' => 'mtgtools_update_symbols',
                '_wpnonce' => wp_create_nonce( 'mtgtools_update_symbols' ),
            ],
            admin_url( 'admin-post.php' )
        );
    }

    /**
     * -----------------
     *   U P D A T E S
     * -----------------
     */

    /**
     * Download and install latest updates
     * 
     * @hooked admin_post_mtgtools_update_symbols
     * @return array query args to append to redirect url
     */
    public function update_symbols() : array
    {
        $successful = false;
        return [
            'action' => $successful ? 'updated' : 'checked_current'
        ];
    }

    /**
     * Check for available updates
     */
    private function updates_available() : bool
    {
        return false;
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