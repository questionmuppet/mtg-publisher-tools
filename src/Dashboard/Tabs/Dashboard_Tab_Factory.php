<?php
/**
 * Dashboard_Tab_Factory
 * 
 * Creates dashboard tab objects
 */

namespace Mtgtools\Dashboard\Tabs;
use Mtgtools\Abstracts\Factory;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Dashboard_Tab_Factory extends Factory
{
    /**
     * Object definition
     */
    protected $type_map = [
        'simple' => 'Dashboard_Tab',
    ];
    protected $default_type = 'simple';
    protected $base_class   = 'Dashboard_Tab';
    protected $namespace    = __NAMESPACE__;

    /**
     * Create dashboard tab
     */
    public function create_tab( array $params ) : Dashboard_Tab
    {
        return $this->create_object( $params );
    }

}   // End of class