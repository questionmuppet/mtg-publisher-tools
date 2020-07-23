<?php
/**
 * Mtgtools_Cron
 * 
 * Schedules and cancels automated update checks
 */

namespace Mtgtools;
use Mtgtools\Abstracts\Module;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Cron extends Module
{
    /**
     * Constants
     */
    const CRON_HOOK = 'mtgtools_cron_hook';

    /**
     * Dependencies
     */
    private $updates;

    /**
     * Constructor
     */
    public function __construct( Mtgtools_Updates $updates, $plugin )
    {
        $this->updates = $updates;
        parent::__construct( $plugin );
    }

    /**
     * Add WordPress hooks
     */
    public function add_hooks() : void
    {
        add_action( self::CRON_HOOK, array( $this->updates(), 'check_for_updates' ) );
        add_action( 'mtgtools_save_option_check_for_updates', array( $this, 'on_checks_enabled_change' ), 10, 2 );
    }
    
    /**
     * ---------------------------
     *   O P T I O N   H O O K S
     * ---------------------------
     */

    /**
     * Update schedule when checks-enabled option changes
     */
    public function on_checks_enabled_change( $new, $old ) : void
    {
        if ( $new != $old ) // Loose comparison so 1 = true
        {
            $this->set_schedule( $new );
        }
    }

    /**
     * ---------------------
     *   S C H E D U L E R
     * ---------------------
     */

    /**
     * (Re)set update-check schedule
     * 
     * @param bool $enable Whether to enable automated checks
     */
    public function set_schedule( bool $enable ) : void
    {
        $this->cancel_update_checks();
        if ( $enable )
        {
            $this->schedule_update_checks();
        }
    }
    
    /**
     * Enable automated update checks
     */
    public function schedule_update_checks() : void
    {
        if ( !wp_next_scheduled( self::CRON_HOOK ) )
        {
            wp_schedule_event( time(), $this->get_interval_key(), self::CRON_HOOK );
        }
    }

    /**
     * Disable automated update checks
     */
    public function cancel_update_checks() : void
    {
        $timestamp = wp_next_scheduled( self::CRON_HOOK );
        wp_unschedule_event( $timestamp, self::CRON_HOOK );
    }
    
    /**
     * ---------------------
     *   I N T E R V A L S
     * ---------------------
     */

    /**
     * Get cron interval key
     */
    private function get_interval_key() : string
    {
        return $this->updates()->get_update_period('for_cron');
    }

    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * Get updates module
     */
    private function updates() : Mtgtools_Updates
    {
        return $this->updates;
    }

}   // End of class