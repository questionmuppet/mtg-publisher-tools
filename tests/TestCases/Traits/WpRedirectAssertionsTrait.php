<?php
declare(strict_types=1);

namespace Mtgtools\Tests\TestCases\Traits;

use Mtgtools\Tests\TestCases\Exceptions\WpRedirectAttemptException;

/**
 * Assertions to track how wp_redirect() was called. For these to work, you must
 * call the register_redirect_handler() method first. The best place to do this is
 * usually during setUp.
 * 
 * If you're extending WordPress testcases, they will automatically remove filters
 * added by the test during tearDown. Otherwise you should do this yourself by
 * calling remove_redirect_handler().
 */
trait WpRedirectAssertionsTrait
{
    /**
     * Last attempted redirect
     */
    private $_last_redirect;
    
    /**
     * -----------------
     *   H A N D L E R
     * -----------------
     */

    /**
     * Register redirect handler function
     */
    protected function register_redirect_handler() : void
    {
        add_filter( 'wp_redirect', array( $this, 'wp_redirect_handler' ), 10, 2 );
    }

    /**
     * Remove the redirect handler function
     */
    protected function remove_redirect_handler() : void
    {
        remove_filter( 'wp_redirect', array( $this, 'wp_redirect_handler' ), 10 );
    }
    
    /**
     * Intercept wp_redirect() and store call values
     * 
     * @hooked wp_redirect
     */
    public function wp_redirect_handler( $location, $status ) : string
    {
        $this->_last_redirect = [
            'location' => $location,
            'status' => $status,
        ];
        throw new WpRedirectAttemptException( "A wp_redirect() attempt was intercepted and cancelled. You can catch this exception and use the family of assertWpRedirect assertions to check the parameters." );
    }

    /**
     * -----------------------
     *   A S S E R T I O N S
     * -----------------------
     */

    /**
     * Expect a redirect attempt with the provided parameters
     */
    protected function expectWpRedirect( string $message = '' ) : void
    {
        $this->expectException( WpRedirectAttemptException::class, $message );
    }

    /**
     * Assert the value of the redirect location
     */
    protected function assertWpRedirectLocationEquals( string $value, string $message = '' ) : void
    {
        $this->assertEquals(
            $value,
            $this->_last_redirect['location'] ?? '',
            $message
        );
    }

    /**
     * Assert a string is contained in the redirect location
     */
    protected function assertWpRedirectLocationContains( string $value, string $message = '' ) : void
    {
        $this->assertStringContainsString(
            $value,
            $this->_last_redirect['location'] ?? '',
            $message
        );
    }

    /**
     * Assert the value of the redirect HTTP status code
     */
    protected function assertWpRedirectStatusEquals( int $value, string $message = '' ) : void
    {
        $this->assertEquals(
            $value,
            $this->_last_redirect['status'] ?? 0,
            $message
        );
    }
}
