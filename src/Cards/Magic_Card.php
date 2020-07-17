<?php
/**
 * Magic_Card
 * 
 * Represents a single printing of a Magic card
 */

namespace Mtgtools\Cards;
use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Magic_Card extends Data
{
    /**
     * Required properties
     */
    protected $required = array(
        'uuid',
        'name',
        'set_code',
        'collector_number',
        'language',
    );

    /**
     * Default properties
     */
    protected $defaults = [
        'images' => [],
    ];

    /**
     * Constructor
     */
    public function __construct( $props )
    {
        parent::__construct( $props );
        foreach ( $this->get_images() as $image )
        {
            if ( !$image instanceof Image_Uri )
            {
                throw new \DomainException(
                    sprintf(
                        "Tried to instantiate a %s with invalid image uri. Images passed in the constructor must be instances of 'Image_Uri'.",
                        get_called_class()
                    )
                );
            }
        }
    }

    /**
     * ---------------
     *   I M A G E S
     * ---------------
     */
    
    /**
     * Get image with type preference
     * 
     * @param string $type  Preferred type to retrieve
     * @return Image_Uri    Image uri matching specified type or highest priority image available
     * @throws UnexpectedValueException
     */
    public function get_image( string $type = '' ) : Image_Uri
    {
        return $this->has_image( $type )
            ? $this->get_images()[ $type ]
            : $this->get_best_image();
    }

    /**
     * Get highest priority image
     */
    private function get_best_image() : Image_Uri
    {
        if ( !count( $this->get_images() ) )
        {
            throw new \UnexpectedValueException( get_called_class() . " tried to retrieve a missing image uri. No image could be found for card '{$this->get_name_with_edition()}'." );
        }
        return $this->get_first_image();
    }
    
    /**
     * Get first image
     */
    private function get_first_image() : Image_Uri
    {
        $images = $this->get_images();
        $key = array_key_first( $images );
        return $images[ $key ];
    }

    /**
     * Check for image by type
     */
    public function has_image( string $type ) : bool
    {
        return array_key_exists( $type, $this->get_images() );
    }
    
    /**
     * Get all images
     */
    public function get_images() : array
    {
        return $this->get_prop( 'images' );
    }
    
    /**
     * -----------------------
     *   P R O P E R T I E S
     * -----------------------
     */

    /**
     * Get human-readable name with edition information
     */
    public function get_name_with_edition() : string
    {
        return sprintf( "%s (%s)", $this->get_name(), $this->get_set_code() );
    }

    /**
     * Get uuid (unique to data source)
     */
    public function get_uuid() : string
    {
        return $this->get_prop( 'uuid' );
    }

    /**
     * Get Oracle card name
     */
    public function get_name() : string
    {
        return $this->get_prop( 'name' );
    }

    /**
     * Get set code
     */
    public function get_set_code() : string
    {
        return $this->get_prop( 'set_code' );
    }

    /**
     * Get collector number
     */
    public function get_collector_number() : string
    {
        return $this->get_prop( 'collector_number' );
    }

    /**
     * Get language
     */
    public function get_language() : string
    {
        return $this->get_prop( 'language' );
    }

}   // End of class