<?php
/**
 * Post_Handler_Factory
 * 
 * Creates Admin_Post_Handler objects
 */

namespace Mtgtools\Admin_Post;
use Mtgtools\Admin_Post\Interfaces\Admin_Post_Responder;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Post_Handler_Factory
{
    /**
     * Responder type-to-class map
     */
    protected $responder_type_map = [
        'ajax' => 'Ajax_Responder',
    ];
    
    /**
     * Default response type
     */
    protected $default_type = 'ajax';

    /**
     * Create new handler
     */
    public function create_handler( array $params ) : Admin_Post_Handler
    {
        $params['type'] = $params['type'] ?? $this->get_default_type();
        $responder = $this->create_responder( $params['type'] );
        return new Admin_Post_Handler( $params, $responder );
    }

    /**
     * Create new responder
     */
    private function create_responder( string $type ) : Admin_Post_Responder
    {
        $class = $this->get_responder_class( $type );
        return new $class();
    }
    
    /**
     * Get responder class by type
     */
    private function get_responder_class( string $type ) : string
    {
        if ( !array_key_exists( $type, $this->responder_type_map ) )
        {
            throw new \OutOfRangeException(
                sprintf(
                    "%s tried to instantiate an Admin_Post_Responder of invalid type '%s'.",
                    get_called_class(),
                    $type
                )
            );
        }
        return __NAMESPACE__ . '\\'. $this->responder_type_map[ $type ];
    }

    /**
     * Get default type
     */
    private function get_default_type() : string
    {
        return $this->default_type;
    }

}   // End of class