<?php
declare(strict_types=1);

namespace Mtgtools\Tests\TestCases\Traits;

/**
 * Assertions to test parameters passed to wp_die()
 * 
 * To use these assertions, call register_wp_exit_tracker() during setUp. Be
 * sure to call remove_wp_exit_tracker() during tearDown as well, to ensure
 * the filter doesn't corrupt later tests.
 */
trait WpExitAssertionsTrait
{
    /**
     * Params passed to last wp_die() call
     */
    private $wp_die_params;

    /**
     * -----------------
     *   H A N D L E R
     * -----------------
     */

    /**
     * Add filter to track params used to call wp_die()
     */
    protected function register_wp_exit_tracker() : void
    {
        $this->wp_die_params = [];
        add_filter(
            'wp_die_handler',
            array( $this, 'get_save_params_handler' ),
            20  // Later priority to override default
        );
    }

    /**
     * Remove exit tracking filter
     */
    protected function remove_wp_exit_tracker() : void
    {
        remove_filter( 'wp_die_handler', array( $this, 'get_save_params_handler' ) );
    }

    /**
     * Get custom wp_die handler
     */
    public function get_save_params_handler() : callable
    {
        return array( $this, 'save_wp_die_params' );
    }
    
    /**
     * Saves parameters used to call wp_die() and throws exception
     * 
     * @see https://developer.wordpress.org/reference/functions/wp_die/
     * 
     * @param mixed $message
     * @param mixed $title
     * @param mixed $args
     * @throws WPDieException
     */
    public function save_wp_die_params( $message = '', $title = '', $args = [] ) : void
    {
        $this->wp_die_params = array(
            'message' => $message,
            'title' => $title,
            'args' => $args,
        );

        // Call default handler in WP_UnitTestCase_Base
        $this->wp_die_handler($message, $title, $args);
    }

    /**
     * -----------------------
     *   A S S E R T I O N S
     * -----------------------
     */

    /**
     * Get parameters passed to wp_die() by key
     * 
     * @return mixed
     */
    protected function get_wp_die_params( string $key )
    {
        return $this->wp_die_params[ $key ] ?? null;
    }
}
