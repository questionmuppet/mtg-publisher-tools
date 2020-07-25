<?php
/**
 * MtgParameterException
 * 
 * Exception thrown when a request for Magic card data contains invalid parameters
 */

namespace Mtgtools\Exceptions\Mtg;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class MtgParameterException extends MtgDataException
{

}