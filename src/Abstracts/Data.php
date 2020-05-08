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
                throw new \DomainException( get_called_class() . " could not be created without required property {$key}." );
            }
        }
        $this->props = wp_parse_args(
            $props,
            array_merge( $this->abstract_defaults, $this->defaults )
        );
    }

    /**
     * Get property
     */
    protected function get_prop( string $key )
    {
        return $this->props[ $key ] ?? null;
    }

    /**
     * Set property
     */
    protected function set_prop( string $key, $value )
    {
        $this->props[ $key ] = $value;
    }

    /**
     * Delete property
     */
    protected function delete_prop( string $key )
    {
        unset( $this->props[ $key ] );
    }

}   // End of class