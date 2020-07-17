<?php
/**
 * ScryfallException
 * 
 * Exception thrown when a call to retrieve Scryfall data encounters an error
 */

namespace Mtgtools\Exceptions\Sources\Scryfall;

use Mtgtools\Exceptions\Sources\MtgSourceException;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class ScryfallException extends MtgSourceException
{

}