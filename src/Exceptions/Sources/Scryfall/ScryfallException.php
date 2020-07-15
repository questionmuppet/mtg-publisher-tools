<?php
/**
 * ScryfallException
 * 
 * Exception thrown when a call to retrieve Scryfall data encounters an error
 */

namespace Mtgtools\Exceptions\Sources\Scryfall;

use Mtgtools\Exceptions\Sources\MtgDataSourceException;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class ScryfallException extends MtgDataSourceException
{

}