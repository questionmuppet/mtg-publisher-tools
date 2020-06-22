<?php
/**
 * Scryfall_Request
 * 
 * Performs a call to the Scryfall API and returns the result
 */

namespace Mtgtools\Scryfall\Requests;
use Mtgtools\Abstracts\Data;
use Mtgtools\Api;
use Mtgtools\Exceptions\Api as Exceptions;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Scryfall_Request extends Data
{
    /**
     * Required properties
     */
    protected $required = array(
        'expects'
    );

    /**
     * Default properties
     */
    protected $defaults = array(
        'endpoint'    => '',
        'base_url'    => 'https://api.scryfall.com/',
        'full_url'    => null,
        'method'      => 'GET',
        'body'        => null,
    );

    /**
     * Cached response data
     */
    private $response;

    /**
     * Get results from the API call
     */
    public function get_data() : array
    {
        if ( !isset( $this->response ) )
        {
            $this->response = $this->fetch();
        }
        return $this->response;
    }
    
    /**
     * Fetch from remote host
     * 
     * @throws ApiException
     */
    protected function fetch() : array
    {
        // Perform request
        $request = new Api\Http_Request( $this->get_request_params() );
        $api_call = new Api\Api_Call( $request );
        $response = $api_call->get_result();

        // Check response
        if ( !$this->is_expected_type( $response ) )
        {
            throw new Exceptions\ScryfallDataException( "A Scryfall API call returned an unexpected response type. Expected type: '{$this->get_expected_type()}'; returned type: '{$response['object']}'." );
        }
        return $response;
    }

    /**
     * Get parameters for HTTP request
     */
    private function get_request_params() : array
    {
        return array_filter([
            'url'    => $this->get_full_url(),
            'method' => $this->get_prop( 'method' ),
            'body'   => $this->get_prop( 'body' ),
        ]);
    }

    /**
     * Get full url
     */
    private function get_full_url() : string
    {
        return $this->get_prop( 'full_url' ) ?? $this->get_base_url() . $this->get_endpoint();
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
    protected function is_expected_type( array $response ) : bool
    {
        return $this->get_expected_type() === $response['object'] ?? '';
    }

    /**
     * Get expected response type
     */
    protected function get_expected_type() : string
    {
        return $this->get_prop( 'expects' );
    }

    /**
     * Get endpoint
     */
    private function get_endpoint() : string
    {
        return $this->get_prop( 'endpoint' );
    }

}   // End of class