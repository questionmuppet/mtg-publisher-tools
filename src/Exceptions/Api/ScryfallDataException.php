<?php
/**
 * ScryfallDataException
 * 
 * Exception thrown when a Scryfall API request returns an unexpected data type
 */

namespace Mtgtools\Exceptions\Api;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class ScryfallDataException extends ApiException
{

}