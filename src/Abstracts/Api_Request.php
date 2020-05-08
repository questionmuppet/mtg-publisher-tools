<?php
/**
 * Api_Request
 * 
 * Performs an HTTP request to an external API
 */

namespace Mtgtools\Abstracts;
use \Mtgtools\Exceptions;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Api_Request extends Data
{
    /**
     * Response object
     */
    private $response;

    /**
     * Required properties
     */
    protected $required = array(
        'base_url',
        'endpoint',
    );

    /**
     * Default properties
     */
    protected $abstract_defaults = array(
        'method'  => '',
        'valid_methods' => [ 'GET', 'POST', 'HEAD' ],
        'default_method' => 'GET',
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
     * 
     * @throws HttpRequestException
     * @return array|object
     */
    public function get_response_body()
    {
        $status = $this->get_status_code();
        if ( "200" !== $status )
        {
            $class = $this->get_exception_class( $status );
            $message = "An external API call returned an invalid HTTP status code. {$this->get_full_url()} returned {$status}: " . $this->get_status_message();
            throw new $class( $message );
        }
        return wp_remote_retrieve_body( $this->get_response() );
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
     * Get Exception class name corresponding to error status
     */
    private function get_exception_class( string $status ) : string
    {
        $coda = $status >= 500
            ? "Http500StatusException"
            : (
                $status >= 400
                ? "Http400StatusException"
                : "HttpStatusException"
            );
        return "Mtgtools\\Exceptions\\" . $coda;
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
            $this->response = wp_remote_request( $this->get_full_url(), $this->get_request_args() );
            if ( is_wp_error( $this->response ) )
            {
                throw new Exceptions\HttpConnectionException( "Failed to make an HTTP connection to an external API. No response received from URL {$this->get_full_url()}." );
            }
        }
        return $this->response;
    }

    /**
     * Get full (sanitized) URL for the request
     */
    private function get_full_url() : string
    {
        return esc_url_raw( $this->get_base_url() . $this->get_endpoint() );
    }

    /**
     * Get the API's base URL, with trailing slash
     */
    private function get_base_url() : string
    {
        return trailingslashit( $this->get_prop( 'base_url' ) );
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
     * Get endpoint to retrieve
     */
    private function get_endpoint() : string
    {
        return $this->get_prop( 'endpoint' );
    }

    /**
     * Get HTTP request method
     */
    private function get_method() : string
    {
        $method = $this->get_prop( 'method' );
        return $this->is_valid_method( $method ) ? $method : $this->get_default_method();
    }

    /**
     * Check for valid HTTP request method
     */
    private function is_valid_method( string $method ) : bool
    {
        return in_array( $method, $this->get_prop( 'valid_methods' ) );
    }

    /**
     * Get default HTTP request method
     */
    private function get_default_method() : string
    {
        return $this->get_prop( 'default_method' );
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