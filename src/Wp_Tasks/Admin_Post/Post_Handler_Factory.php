<?php
/**
 * Post_Handler_Factory
 * 
 * Creates Wp_Tasks for handling user admin requests
 */

namespace Mtgtools\Wp_Tasks\Admin_Post;
use Mtgtools\Abstracts\Factory;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Post_Handler_Factory extends Factory
{
    /**
     * Responder object definition
     */
    protected $type_map = [
        'ajax' => 'Ajax_Responder',
    ];
    protected $default_type = 'ajax';
    protected $base_class = 'Admin_Request_Responder';
    protected $namespace = __NAMESPACE__;

    /**
     * -----------------
     *   O B J E C T S
     * -----------------
     */

    /**
     * Create admin-post handler
     */
    public function create_handler( array $params ) : Admin_Post_Handler
    {
        $type = $this->pop_type( $params );
        $processor = $this->create_processor();
        $responder = $this->create_responder( $type );
        return new Admin_Post_Handler( $processor, $responder, $params );
    }

    /**
     * Create admin-request processor
     */
    public function create_processor( array $params = [] ) : Admin_Request_Processor
    {
        return new Admin_Request_Processor( $params );
    }

    /**
     * Create admin-request responder
     */
    public function create_responder( string $type ) : Admin_Request_Responder
    {
        return $this->create_object([ 'type' => $type ]);
    }

    /**
     * -------------
     *   P R O P S
     * -------------
     */
    
    /**
     * Pop type off of params array and return value
     */
    private function pop_type( array &$params ) : string
    {
        $type = $params['type'] ?? '';
        unset( $params['type'] );
        return $type;
    }

}   // End of class