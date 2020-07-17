<?php
/**
 * MtgFetchException
 * 
 * Exception thrown when a remote fetch of Magic card data fails
 */

namespace Mtgtools\Exceptions\Mtg;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class MtgFetchException extends MtgDataException
{

}