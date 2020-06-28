<?php
/**
 * Table_Data
 * 
 * Exposes data to be used in tables on admin screens
 */

namespace Mtgtools\Dashboard;
use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Table_Data extends Data
{
    /**
     * Required properties
     */
    protected $required = array(
        'id',
        'fields',
        'row_callback',
    );

    /**
     * Table field objects
     */
    private $fields;

    /**
     * Filter to apply to row data
     */
    private $filter = '';

    /**
     * Get table fields
     * 
     * @return Table_Field[]
     */
    public function get_fields() : array
    {
        if ( !isset( $this->fields ) )
        {
            $fields = [];
            foreach ( $this->get_field_defs() as $key => $params )
            {
                $params['id'] = $key;
                $fields[ $key ] = new Table_Field( $params );
            }
            $this->fields = $fields;
        }
        return $this->fields;
    }

    /**
     * Get table row data
     */
    public function get_rows() : array
    {
        return call_user_func( $this->get_row_callback(), $this->filter );
    }

    /**
     * Set data filter
     */
    public function set_filter( string $filter ) : void
    {
        $this->filter = $filter;
    }

    /**
     * -------------
     *   P R O P S
     * -------------
     */

    /**
     * Get unique table identifier
     */
    public function get_key() : string
    {
        return $this->get_prop( 'id' );
    }

    /**
     * Get field definitions
     */
    private function get_field_defs() : array
    {
        return $this->get_prop( 'fields' );
    }

    /**
     * Get row data callback
     */
    private function get_row_callback() : callable
    {
        return $this->get_prop( 'row_callback' );
    }

}   // End of class