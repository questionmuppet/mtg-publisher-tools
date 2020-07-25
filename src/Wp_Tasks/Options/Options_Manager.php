<?php
/**
 * Options_Manager
 * 
 * Lazy-instantiates plugin options from definition parameters
 */

namespace Mtgtools\Wp_Tasks\Options;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Options_Manager
{
    /**
     * Cached plugin options
     * 
     * @var Plugin_Option[]
     */
    private $options = [];

    /**
     * Option definitions
     */
    private $definitions = [];

    /**
     * Option factory
     */
    private $factory;

    /**
     * Constructor
     */
    public function __construct( Option_Factory $factory )
    {
        $this->factory = $factory;
    }

    /**
     * -------------
     *   S E T U P
     * -------------
     */

    /**
     * Reset options in database to their defaults
     */
    public function reset_defaults() : void
    {
        $this->load_all_options();
        foreach ( $this->options as $opt )
        {
            $opt->add_to_db();
        }
    }

    /**
     * Delete all options from database
     */
    public function delete_options() : void
    {
        $this->load_all_options();
        foreach ( $this->options as $opt )
        {
            $opt->delete();
        }
    }
    
    /**
     * Load all options
     */
    private function load_all_options() : void
    {
        foreach ( $this->definitions as $key => $params )
        {
            $this->get_option( $key );
        }
    }

    /**
     * -----------------
     *   O P T I O N S
     * -----------------
     */

    /**
     * Register a new option with the manager
     */
    public function register_option( string $key, array $params ) : void
    {
        if ( empty( $key ) )
        {
            throw new \DomainException(
                sprintf(
                    "%s tried to register an option with invalid parameters. You must provide a keyname for the option.",
                    get_called_class()
                )
            );
        }
        if ( $this->is_defined( $key ) )
        {
            throw new \DomainException(
                sprintf(
                    "%s tried to register a duplicate option. An option is already defined for key '%s'.",
                    get_called_class(),
                    $key
                )
            );
        }
        $params['id'] = $key;
        $this->definitions[ $key ] = $params;
    }
    
    /**
     * Get a registered plugin option
     */
    public function get_option( string $key ) : Plugin_Option
    {
        if ( !array_key_exists( $key, $this->options ) )
        {
            $this->options[ $key ] = $this->load_option( $key );
        }
        return $this->options[ $key ];
    }

    /**
     * Load option from provided definitions
     */
    private function load_option( string $key ) : Plugin_Option
    {
        if ( !$this->is_defined( $key ) )
        {
            throw new \OutOfRangeException(
                sprintf(
                    "%s tried to create an undefined plugin option. No parameters provided for option with key '%s'.",
                    get_called_class(),
                    $key
                )
            );
        }
        return $this->factory()->create_option( $this->definitions[ $key ] );
    }

    /**
     * Check if option key is defined
     */
    private function is_defined( string $key ) : bool
    {
        return array_key_exists( $key, $this->definitions );
    }

    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * Get option factory
     */
    private function factory() : Option_Factory
    {
        return $this->factory;
    }

}   // End of class