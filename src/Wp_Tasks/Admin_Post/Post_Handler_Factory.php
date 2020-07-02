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
        'ajax' => 'Ajax_Handler',
        'redirect' => 'Redirect_Handler',
    ];
    protected $default_type = 'ajax';
    protected $base_class = 'Admin_Post_Handler';
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
        $processor = $this->create_processor();
        return $this->create_object( $params, $processor );
    }

    /**
     * Create admin-request processor
     */
    public function create_processor( array $params = [] ) : Admin_Request_Processor
    {
        return new Admin_Request_Processor( $params );
    }

}   // End of class