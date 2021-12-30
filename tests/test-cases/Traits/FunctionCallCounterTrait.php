<?php
declare(strict_types=1);

/**
 * Function Call Counter
 * 
 * Adds static methods to count the number of calls to an external (i.e. mocked) function
 */
trait FunctionCallCounterTrait
{
    /**
     * Counters
     */
    static public $counters = [];

    /**
     * Set call counters back to zero
     */
    static protected function reset_call_counters( array $keys = [] ) : void
    {
        self::$counters = array_fill_keys( $keys, 0 );
    }

    /**
     * Augment call count
     */
    static public function augment_call_count( string $key ) : void
    {
        self::$counters[ $key ]++;
    }

    /**
     * Get call count by key
     */
    static protected function get_call_count( string $key ) : int
    {
        return self::$counters[ $key ];
    }

}   // End of trait