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
use Mtgtools\Exceptions\Api\ApiException;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Updates extends Module
{
    /**
     * Constants
     */
    const TRANSIENT = 'mtgtools_updates_available';

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
        
        $tab_url = $this->get_dashboard_url('updates');
        $this->register_post_handlers([
            [
                'type'         => 'redirect',
                'action'       => 'mtgtools_update_symbols',
                'callback'     => array( $this, 'update_symbols' ),
                'redirect_url' => $tab_url,
                'error_link'   => [
                    'url' => $tab_url,
                    'text' => 'Return to updates',
                ],
            ],
            [
                'type'         => 'redirect',
                'action'       => 'mtgtools_check_updates',
                'callback'     => array( $this, 'check_for_updates' ),
                'redirect_url' => $tab_url,
                'error_link'   => [
                    'url' => $tab_url,
                    'text' => 'Return to updates',
                ],
            ],
            [
                'type' => 'redirect',
                'action' => 'mtgtools_disable_notices',
                'callback' => array( $this, 'disable_notices' ),
                'redirect_url' => $this->get_dashboard_url('settings'),
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
     * Get status info for display on dashboard
     */
    public function get_status_info() : array
    {
        return [
            'Source' => $this->source->get_display_name(),
            'Link' => $this->get_source_link(),
            'Last checked' => $this->get_last_checked(),
            'Status' => $this->get_update_status(),
        ];
    }

    /**
     * Get source name enclosed in a link
     */
    public function get_nice_source_link() : string
    {
        return sprintf(
            '<a href="%s" target="_blank">%s</a>',
            esc_url( $this->source->get_documentation_uri() ),
            esc_html( $this->source->get_display_name() )
        );
    }

    /**
     * Get link to source documentation
     */
    private function get_source_link() : string
    {
        $url = $this->source->get_documentation_uri();
        return sprintf(
            '<a href="%s" target="_blank">%s</a>',
            esc_url( $url ),
            esc_html( $url )
        );
    }

    /**
     * Get update status for display
     */
    private function get_update_status() : string
    {
        return $this->updates_pending()
            ? '<span style="color: red;">New Magic card data is available for download.</span>'
            : 'No updates are currently pending.';
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
        if ( $this->showing_notices() && $this->updates_pending() && !$this->was_just_checked() )
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
                        'href' => $this->get_disable_notices_link(),
                    ],
                ],
            ]);
        }
    }

    /**
     * Turn off admin notices
     */
    public function disable_notices() : array
    {
        $this->update_plugin_option( 'show_update_notices', false );
        return [ 'action' => 'notices_disabled' ];
    }

    /**
     * Check if we're showing admin notices
     */
    private function showing_notices() : bool
    {
        return $this->get_plugin_option('show_update_notices');
    }

    /**
     * Check if we're returning from an update check action
     */
    private function was_just_checked() : bool
    {
        $action = $_GET['action'] ?? '';
        return preg_match( '/^checked_/', $action );
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
     * Get disable notices action link
     */
    private function get_disable_notices_link() : string
    {
        return add_query_arg(
            [
                'action' => 'mtgtools_disable_notices',
                '_wpnonce' => wp_create_nonce( 'mtgtools_disable_notices' ),
            ],
            admin_url( 'admin-post.php' )
        );
    }

    /**
     * ---------------------
     *   E X E C U T I O N
     * ---------------------
     */

    /**
     * Download and install latest updates
     * 
     * @hooked admin_post_mtgtools_update_symbols
     * @return array query args to append to redirect url
     */
    public function update_symbols() : array
    {
        try
        {
            $count = 0;
            foreach ( $this->source->get_mana_symbols() as $symbol )
            {
                $count += intval(
                    $this->db_ops->add_symbol( $symbol )
                );
            }
            $changes = $this->get_new_symbol_updates();
            if ( isset( $changes['delete'] ) )
            {
                foreach ( $changes['delete'] as $key )
                {
                    $count += intval(
                        $this->db_ops->delete_symbol( $key )
                    );
                }
            }
            $action = $count ? 'updated' : 'checked_current';
            $this->set_last_checked();
            delete_transient( self::TRANSIENT );
        }
        catch ( ApiException $e )
        {
            $action = 'failed';
        }
        return [ 'action' => $action ];
    }

    /**
     * Check for available updates and store as transient
     * 
     * @hooked admin_post_mtgtools_check_updates
     * @return array query args to append to redirect url
     */
    public function check_for_updates() : array
    {
        try
        {
            $instructions = $this->get_new_symbol_updates();
            if ( count( $instructions ) )
            {
                set_transient( self::TRANSIENT, $instructions, $this->get_update_period() );
                $action = 'checked_available';
            }
            else
            {
                $action = 'checked_current';
            }
            $this->set_last_checked();
        }
        catch ( ApiException $e )
        {
            $action = 'failed';
        }
        return [ 'action' => $action ];
    }

    /**
     * Check site transients for pending updates
     */
    public function updates_pending() : bool
    {
        return boolval( get_transient( self::TRANSIENT ) );
    }

    /**
     * Get update period
     */
    private function get_update_period() : int
    {
        return intval( $this->get_plugin_option('update_period_in_weeks') ) * WEEK_IN_SECONDS;
    }

    /**
     * -----------------------
     *   C O M P A R I S O N
     * -----------------------
     */

    /**
     * Check data source against db tables and return difference
     * 
     * @return array Associative array of out-of-date records, keyed by action required to fix
     */
    private function get_new_symbol_updates() : array
    {
        $symbols = $this->source()->get_mana_symbols();
        $checker = $this->db_ops()->get_update_checker( $symbols );
        return array_filter([
            'add' => $checker->records_to_add(),
            'update' => $checker->records_to_update(),
            'delete' => $checker->records_to_delete(),
        ]);
    }

    /**
     * ---------------------------
     *   L A S T   C H E C K E D
     * ---------------------------
     */
    
    /**
     * Get last-checked date
     * 
     * @return string Human-readable string matching WordPress date-time settings
     */
    private function get_last_checked() : string
    {
        $unix_time = get_option( 'mtgtools_last_checked' );
        return $unix_time ? wp_date( DATE_RFC850, $unix_time ) : '—';
    }
    
    /**
     * Set the last-checked timestamp
     */
    private function set_last_checked() : void
    {
        update_option( 'mtgtools_last_checked', time() );
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