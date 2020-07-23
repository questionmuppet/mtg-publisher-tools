<?php
/**
 * Option_Factory
 * 
 * Creates plugin options by type
 */

namespace Mtgtools\Wp_Tasks\Options;
use Mtgtools\Abstracts\Factory;

// Exit if accessed directly
defined( 'ABSPATH' ) or die("Don't mess with it!");

class Option_Factory extends Factory
{
    /**
     * Object definition
     */
    protected $type_map = [
        'text' => 'Option_Text',
        'key' => 'Option_Key',
        'number' => 'Option_Number',
        'checkbox' => 'Option_Checkbox',
        'select' => 'Option_Select',
    ];
    protected $default_type = 'text';
    protected $base_class = 'Plugin_Option';
    protected $namespace = __NAMESPACE__;

    /**
     * Create a plugin option
     */
    public function create_option( array $params ) : Plugin_Option
    {
        return $this->create_object( $params );
    }

}   // End of class