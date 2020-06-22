<?php
/**
 * Mtgtools_Dashboard
 * 
 * Creates and displays admin pages
 */

namespace Mtgtools;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Dashboard
{
    /**
     * Enqueue module
     */
    private $enqueue;
    
    /**
     * Constructor
     */
    public function __construct( Mtgtools_Enqueue $enqueue )
    {
        $this->enqueue = $enqueue;
    }

}   // End of class