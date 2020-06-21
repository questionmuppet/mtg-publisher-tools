<?php
/**
 * ScryfallException
 * 
 * Exception thrown when a Scryfall API request encounters an error
 */

namespace Mtgtools\Exceptions\Scryfall;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class ScryfallException extends \RuntimeException
{

}