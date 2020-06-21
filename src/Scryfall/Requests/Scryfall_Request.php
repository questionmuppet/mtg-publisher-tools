<?php
/**
 * Scryfall_Request
 * 
 * Performs a call to the Scryfall API and returns the result
 */

namespace Mtgtools\Scryfall\Requests;
use Mtgtools\Abstracts\Data;
use Mtgtools\Api;
use Mtgtools\Exceptions;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Scryfall_Request extends Data
{
    /**
     * Required properties
     */
    protected $required = array( 'endpoint' );

    /**
     * Default properties
     */
    protected $defaults = array(
        'type'        => '',
        'base_url'    => 'https://api.scryfall.com/',
        'http_params' => [],
    );

    /**
     * Get results from the API call
     */
    abstract public function get_data();
    
    /**
     * Fetch from remote host
     */
    protected function fetch() : \stdClass
    {
        try
        {
            $request = new Api\Http_Request( $this->get_request_params() );
            $api_call = new Api\Api_Call( $request );
            return $api_call->get_result();
        }
        catch ( Exceptions\Http\HttpRequestException $e )
        {
            throw new Exceptions\Scryfall\ScryfallException( $e->getMessage() );
        }
    }

    /**
     * Get parameters for HTTP request
     */
    private function get_request_params() : array
    {
        $params = $this->get_prop( 'http_params' );
        $params['url'] = $this->get_full_url();
        return $params;
    }

    /**
     * Get full url
     */
    private function get_full_url() : string
    {
        return $this->get_base_url() . $this->get_endpoint();
    }

    /**
     * Get base url, with trailing slash
     */
    private function get_base_url() : string
    {
        return trailingslashit( $this->get_prop( 'base_url' ) );
    }

    /**
     * Check response object against expected type
     */
    protected function is_expected_type( \stdClass $response ) : bool
    {
        return $this->get_type() === $response->object;
    }

    /**
     * Get expected response type
     */
    protected function get_type() : string
    {
        return $this->get_prop( 'type' );
    }

    /**
     * Get endpoint
     */
    private function get_endpoint() : string
    {
        return $this->get_prop( 'endpoint' );
    }

}   // End of class