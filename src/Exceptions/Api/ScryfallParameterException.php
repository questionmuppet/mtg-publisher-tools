<?php
/**
 * ScryfallParameterException
 * 
 * Exception thrown when a Scryfall API request is made with invalid or incomplete parameters
 */

namespace Mtgtools\Exceptions\Api;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class ScryfallParameterException extends ApiException
{

}