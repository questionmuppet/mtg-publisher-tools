<?php
/**
 * Api_Call
 * 
 * Performs a call to an external API
 */

namespace Mtgtools\Api;
use Mtgtools\Exceptions\Api as Exceptions;
use Mtgtools\Exceptions\Http\HttpConnectionException;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Api_Call
{
    /**
     * Request object
     */
    private $request;

    /**
     * Constructor
     */
    public function __construct( Http_Request $request )
    {
        $this->request = $request;
    }

    /**
     * Perform the call and return the result
     * 
     * @throws ApiException
     */
    public function get_result() : array
    {
        if ( !$this->status_is_ok() )
        {
            throw new Exceptions\ApiStatusException(
                sprintf(
                    "An external API call returned an unsuccessful HTTP status code '%s: %s'.",
                    $this->request->get_status_code(),
                    $this->request->get_status_message()
                )
            );
        }
        return $this->decode( $this->request->get_response_body() );
    }

    /**
     * Decode JSON response string
     */
    private function decode( string $body ) : array
    {
        $data = json_decode( $body, true );
        if ( is_null( $data ) )
        {
            throw new Exceptions\ApiJsonException( "An API call returned malformed JSON. Cannot retrieve data from '{$this->request->get_sanitized_url()}'." );
        }
        return $data;
    }

    /**
     * Check for okay status
     */
    private function status_is_ok() : bool
    {
        try
        {
            return "200" === $this->request->get_status_code();
        }
        catch ( HttpConnectionException $e )
        {
            throw new Exceptions\ApiConnectionException( $e->getMessage() );
        }
    }

}   // End of class