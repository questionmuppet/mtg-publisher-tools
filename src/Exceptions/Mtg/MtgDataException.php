<?php
/**
 * MtgDataException
 * 
 * Exception thrown when a request for Magic card data fails
 */

namespace Mtgtools\Exceptions\Mtg;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class MtgDataException extends \RuntimeException
{

}