<?php
/**
 * Redirect_Handler
 * 
 * Registers an HTTP 3xx redirection handler for a request to the WordPress admin
 */

namespace Mtgtools\Wp_Tasks\Admin_Post;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Redirect_Handler extends Admin_Post_Handler
{
    /**
     * Default properties
     */
    protected $defaults = array(
        'redirect_url' => '',
        'error_link' => [],
        'back_link' => false,
    );

    /**
     * -----------------
     *   S U C C E S S
     * -----------------
     */

    /**
     * Handle success state
     */
    protected function handle_success( array $result ) : void
    {
        $this->redirect_with_args([
            'action' => $this->get_action(),
            'status' => 'success',
        ]);
    }

    /**
     * Redirect with optional arguments
     * 
     * @param array $query_args     Optional query args to append as $_GET parameters
     */
    private function redirect_with_args( $query_args = [] ) : void
    {
        $url = add_query_arg( $query_args, $this->get_redirect_url() );
        nocache_headers();
        wp_safe_redirect(
            esc_url_raw( $url ),    // WordPress sanitization method
            302                     // Http status: 302 Found
        );
        exit;
    }

    /**
     * -------------
     *   E R R O R
     * -------------
     */
    
    /**
     * Handle error state
     */
    protected function handle_error( \Exception $e ) : void
    {
        wp_die(
            $e->getMessage(),
            $this->get_error_title(),
            $this->get_error_args()
        );
    }

    /**
     * Get error title
     */
    private function get_error_title() : string
    {
        return "Internal Server Error";
    }
    
    /**
     * Get wp_die error arguments
     * 
     * @see https://developer.wordpress.org/reference/functions/wp_die/
     */
    private function get_error_args() : array
    {
        return array_filter([
            'response' => $this->get_http_error_code(),
            'link_url' => $this->get_error_link_url(),
            'link_text' => $this->get_error_link_text(),
            'back_link' => $this->include_back_link(),
        ]);
    }

    /**
     * -------------
     *   P R O P S
     * -------------
     */
    
    /**
     * Get redirect url
     */
    private function get_redirect_url() : string
    {
        $url = $this->get_prop( 'redirect_url' );
        if ( empty( $url ) )
        {
            throw new \InvalidArgumentException( "Missing or invalid redirect url provided to " . get_called_class() . ". You must provide a valid url to redirect to upon success." );
        }
        return $url;
    }

    /**
     * Get HTTP error code
     */
    private function get_http_error_code() : int
    {
        return 500;
    }

    /**
     * Get error link url
     */
    private function get_error_link_url() : string
    {
        return $this->get_prop( 'error_link' )['url'] ?? '';
    }

    /**
     * Get error link text
     */
    private function get_error_link_text() : string
    {
        return $this->get_prop( 'error_link' )['text'] ?? '';
    }

    /**
     * Whether to include a "back" link on the error page
     */
    private function include_back_link() : bool
    {
        return boolval( $this->get_prop( 'back_link' ) );
    }

}   // End of class