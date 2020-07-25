<?php
/**
 * Data
 * 
 * Abstract class for basic data classes
 */

namespace Mtgtools\Abstracts;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Data
{
    /**
     * Required properties
     */
    protected $required = array();

    /**
     * Default properties
     */
    protected $defaults = array();

    /**
     * Defaults set by abstract class
     */
    protected $abstract_defaults = array();

    /**
     * Properties
     */
    private $props;

    /**
     * Constructor
     */
    public function __construct( array $props = [] )
    {
        foreach ( $this->required as $key )
        {
            if ( !isset( $props[ $key ] ) )
            {
                throw new \DomainException( "Instance of " . get_called_class() . " can not be created without required property '{$key}'." );
            }
        }
        $this->props = array_replace(
            $this->abstract_defaults,
            $this->defaults,
            $props
        );
    }
    
    /**
     * Get property in string format
     */
    protected function get_string_prop( string $key ) : string
    {
        $prop = $this->get_prop( $key );
        return is_scalar( $prop ) ? strval( $prop ) : '';
    }

    /**
     * Check if property is defined
     */
    protected function prop_isset( string $key ) : bool
    {
        return !is_null( $this->get_prop( $key ) );
    }

    /**
     * Get property
     * 
     * @return mixed
     */
    protected function get_prop( string $key )
    {
        return $this->props[ $key ] ?? null;
    }

    /**
     * Set multiple properties at once
     */
    protected function set_props( array $new_props ) : void
    {
        $this->props = array_replace( $this->props, $new_props );
    }

    /**
     * Set property
     */
    protected function set_prop( string $key, $value ) : void
    {
        $this->props[ $key ] = $value;
    }

    /**
     * Delete property
     */
    protected function delete_prop( string $key ) : void
    {
        unset( $this->props[ $key ] );
    }

}   // End of class