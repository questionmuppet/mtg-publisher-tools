<?php
/**
 * Scryfall_Request_Factory
 * 
 * Creates Scryfall api requests by type
 */

namespace Mtgtools\Scryfall;
use Mtgtools\Abstracts\Factory;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Scryfall_Request_Factory extends Factory
{
    /**
     * Type-to-class map
     */
    protected $type_map = [
        'card' => 'Scryfall_Object_Request',
        'list' => 'Scryfall_List_Request',
    ];

    /**
     * Default type
     */
    protected $default_type = 'card';

    /**
     * Base class for generated objects
     */
    protected $base_class = 'Scryfall_Request';

    /**
     * Namespace path
     */
    protected $namespace = 'Mtgtools\\Scryfall\\Requests';

    /**
     * Create API request object
     */
    public function create_request( array $params )
    {
        return $this->create_object( $params );
    }

}   // End of class