<?php
/**
 * Factory
 * 
 * Abstract class for creating objects by type
 */

namespace Mtgtools\Abstracts;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

abstract class Factory
{
    /**
     * Object definition
     * 
     * @extended by children
     */
    protected $type_map;
    protected $default_type;
    protected $base_class;
    protected $namespace;

    /**
     * Create a new object
     * 
     * @param array $params     Arguments to pass to the object constructor
     * @param mixed ...$deps    One or more dependencies required by the class; should be type-hinted in constructor
     * @return object           Instantiated object
     */
    protected function create_object( array $params, ...$deps )
    {
        $params['type'] = $params['type'] ?? $this->get_default_type();
        $class = $this->get_class( $params['type'] );
        return new $class( $params, ...$deps );
    }

    /**
     * ---------------------
     *   C L A S S N A M E
     * ---------------------
     */

    /**
     * Get object class by type
     */
    private function get_class( string $type ) : string
    {
        if ( !$this->type_exists( $type ) )
        {
            throw new \OutOfRangeException(
                sprintf(
                    "%s tried to instantiate a %s of invalid type '%s'.",
                    get_called_class(),
                    $this->get_base_class(),
                    $type
                )
            );
        }
        return $this->get_namespace() . "\\" . $this->get_type_map()[ $type ];
    }
    
    /**
     * Check if a type is defined
     */
    private function type_exists( string $type ) : bool
    {
        return array_key_exists( $type, $this->get_type_map() );
    }

    /**
     * -------------------------------------
     *   O B J E C T   D E F I N I T I O N
     * -------------------------------------
     */

    /**
     * Get type-to-class map
     */
    private function get_type_map() : array
    {
        return $this->type_map;
    }

    /**
     * Get default type
     */
    private function get_default_type() : string
    {
        return $this->default_type;
    }

    /**
     * Get base class for generated objects
     */
    private function get_base_class() : string
    {
        return $this->base_class;
    }

    /**
     * Get namespace path
     */
    private function get_namespace() : string
    {
        return $this->namespace;
    }

}   // End of class