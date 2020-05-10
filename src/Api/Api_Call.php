<?php
/**
 * Api_Call
 * 
 * Performs a call to an external API
 */

namespace Mtgtools\Api;
use \Mtgtools\Interfaces\Remote_Request;
use \Mtgtools\Exceptions\Http as Exceptions;

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
    public function __construct( Remote_Request $request )
    {
        $this->request = $request;
    }

    /**
     * Perform the call and return the result
     */
    public function get_result() : array
    {
        if ( !$this->status_is_ok() )
        {
            $this->throw_http_exception( $this->request->get_status_code(), $this->get_error_message() );
        }
        return $this->request->get_response_body();
    }

    /**
     * Check for okay status
     */
    private function status_is_ok() : bool
    {
        return "200" === $this->request->get_status_code();
    }

    /**
     * Throw HTTP exception
     */
    private function throw_http_exception( string $status, string $message ) : void
    {
        if ( $status >= 500 ) {
            throw new Exceptions\Http500StatusException( $message );
        }
        else if ( $status >= 400 ) {
            throw new Exceptions\Http400StatusException( $message );
        }
        else {
            throw new Exceptions\HttpStatusException( $message );
        }
    }
    
    /**
     * Get error message
     */
    private function get_error_message() : string
    {
        return sprintf(
            "An external API call returned an invalid HTTP status code '%s: %s'.",
            $this->request->get_status_code(),
            $this->request->get_status_message()
        );
    }

}   // End of class