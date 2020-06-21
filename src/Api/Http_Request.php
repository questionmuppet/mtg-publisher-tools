<?php
/**
 * Http_Request
 * 
 * Performs an HTTP request and exposes response data
 */

namespace Mtgtools\Api;
use Mtgtools\Exceptions\Http\HttpConnectionException;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Http_Request extends \Mtgtools\Abstracts\Data implements \Mtgtools\Interfaces\Remote_Request
{
    /**
     * Array of response data
     */
    private $response;

    /**
     * Required properties
     */
    protected $required = array(
        'url',
        'method',
    );

    /**
     * Default properties
     */
    protected $defaults = array(
        'valid_methods' => [ 'GET', 'POST', 'HEAD' ],
        'timeout' => 10,
        'body'    => [],
    );
    
    /**
     * -------------
     * O U T P U T S
     * -------------
     */
    
    /**
     * Perform HTTP request and return response body
     */
    public function get_response_body() : array
    {
        return json_decode(
            wp_remote_retrieve_body( $this->get_response() ),
            true
        );
    }
    
    /**
     * Get HTTP status code
     */
    public function get_status_code() : string
    {
        return wp_remote_retrieve_response_code( $this->get_response() );
    }

    /**
     * Get HTTP status message
     */
    public function get_status_message() : string
    {
        return wp_remote_retrieve_response_message( $this->get_response() );
    }
    
    /**
     * -------------
     * R E Q U E S T
     * -------------
     */
    
    /**
     * Get HTTP response
     * 
     * @return array|WP_Error
     */
    private function get_response()
    {
        if ( !isset( $this->response ) )
        {
            $this->response = wp_remote_request( $this->get_sanitized_url(), $this->get_request_args() );
            if ( is_wp_error( $this->response ) )
            {
                throw new HttpConnectionException( "Failed to make an external HTTP connection. No response received from URL {$this->get_sanitized_url()}." );
            }
        }
        return $this->response;
    }
    
    /**
     * Get arguments for the request
     */
    private function get_request_args() : array
    {
        return array_filter([
            'method'  => $this->get_method(),
            'timeout' => $this->get_timeout(),
            'body'    => $this->get_post_body(),
        ]);
    }

    /**
     * -------------------
     * P R O P E R T I E S
     * -------------------
     */

    /**
     * Get sanitized URL for the request
     */
    private function get_sanitized_url() : string
    {
        return esc_url_raw( $this->get_prop( 'url' ) );
    }

    /**
     * Get HTTP request method
     */
    private function get_method() : string
    {
        $method = $this->get_prop( 'method' );
        if ( !$this->is_valid_method( $method ) )
        {
            throw new \DomainException( "An invalid method was provided for an HTTP request. Method '{$method}' is not supported." );
        }
        return $method;
    }

    /**
     * Check for valid HTTP request method
     */
    private function is_valid_method( string $method ) : bool
    {
        return in_array( $method, $this->get_prop( 'valid_methods' ) );
    }

    /**
     * Get timeout setting
     */
    private function get_timeout() : int
    {
        return absint( $this->get_prop( 'timeout' ) );
    }

    /**
     * Get data to send in POST request
     */
    private function get_post_body() : array
    {
        return $this->get_prop( 'body' );
    }

}   // End of class