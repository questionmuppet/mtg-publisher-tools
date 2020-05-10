<?php
/**
 * Scryfall_Request
 * 
 * Performs a call to the Scryfall API and returns the result
 */

namespace Mtgtools\Scryfall;
use \Mtgtools\Api;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Scryfall_Request extends \Mtgtools\Abstracts\Data
{
    /**
     * Required properties
     */
    protected $required = array( 'endpoint' );

    /**
     * Default properties
     */
    protected $defaults = array(
        'base_url'    => 'https://api.scryfall.com/',
        'http_params' => [],
    );

    /**
     * Get results from the API call
     */
    public function get_data() : array
    {
        $request = new Api\Http_Request( $this->get_request_params() );
        $api_call = new Api\Api_Call( $request );
        return $api_call->get_result();
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
     * Get endpoint
     */
    private function get_endpoint() : string
    {
        return $this->get_prop( 'endpoint' );
    }

}   // End of class