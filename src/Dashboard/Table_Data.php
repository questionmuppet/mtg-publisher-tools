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
        'fields',
        'row_data',
    );

    /**
     * Constructor
     */
    public function __construct( array $props )
    {
        parent::__construct( $props );
        foreach ( $this->get_rows() as $row )
        {
            if ( !is_array( $row ) )
            {
                throw new \InvalidArgumentException( get_called_class() . " was passed invalid row data in the constructor. Row data must consist of an array of associative arrays, each keyed by field id." );
            }
        }
    }

    /**
     * Table field objects
     */
    private $fields;

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
            foreach ( $this->get_prop( 'fields' ) as $key => $field_defs )
            {
                $field_defs['id'] = $key;
                $fields[ $key ] = new Table_Field( $field_defs );
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
        return $this->get_prop( 'row_data' );
    }

}   // End of class