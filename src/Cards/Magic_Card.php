<?php
/**
 * Magic_Card
 * 
 * Represents a single printing of a Magic card
 */

namespace Mtgtools\Cards;
use Mtgtools\Abstracts\Data;
use Mtgtools\Exceptions\Cache as Exceptions;

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
     * ---------------------
     *   I M A G E   U R I
     * ---------------------
     */

    /**
     * Get image uri by type
     * 
     * @throws CacheException
     */
    public function get_image_uri( string $type ) : string
    {
        $image = $this->get_image( $type );
        if ( $image->is_expired() )
        {
            throw new Exceptions\ExpiredDataException( "Requested an expired image uri. Image of type '{$type}' for card '{$this->get_name()}' needs to be refreshed in the cache." );
        }
        return $image->get_uri();
    }
    
    /**
     * Get image by type
     */
    private function get_image( string $type ) : Image_Uri
    {
        if ( !$this->has_image( $type ) )
        {
            throw new Exceptions\MissingDataException( "Requested a missing card image. No image of type '{$type}' cached for card '{$this->get_name()}'." );
        }
        return $this->get_images()[ $type ];
    }

    /**
     * Check for image by type
     */
    private function has_image( string $type ) : bool
    {
        return array_key_exists( $type, $this->get_images() );
    }

    /**
     * -----------------------
     *   P R O P E R T I E S
     * -----------------------
     */

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
    
    /**
     * Get images
     */
    public function get_images() : array
    {
        return $this->get_prop( 'images' );
    }

}   // End of class