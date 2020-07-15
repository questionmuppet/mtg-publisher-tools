<?php
/**
 * MtgDataSourceException
 * 
 * Exception thrown when a call to an external MTG_Data_Source encounters an error
 */

namespace Mtgtools\Exceptions\Sources;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class MtgDataSourceException extends \RuntimeException
{

}