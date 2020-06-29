<?php
/**
 * Template
 * 
 * Loads a template file, allowing for themes to override
 */

namespace Mtgtools\Tasks\Templates;
use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Template extends Data
{
    /**
     * Required properties
     */
    protected $required = array(
        'path',
    );

    /**
     * Default properties
     */
    protected $defaults = array(
        'base_dir'     => MTGTOOLS__TEMPLATE_PATH,
        'vars'         => [],
        'require_once' => false,
    );

    /**
     * -----------------
     *   I N C L U D E
     * -----------------
     */
    
    /**
     * Echo template file as output
     */
    public function include() : void
    {
        $this->set_query_vars();
        load_template( $this->locate_template(), $this->require_once() );
        $this->remove_query_vars();
    }

    /**
     * Locate highest priority template file
     */
    private function locate_template() : string
    {
        $template = locate_template( $this->get_path() );
        return strlen( $template )
            ? $template
            : $this->get_default_path();
    }

    /**
     * Get path to default version of template
     */
    private function get_default_path() : string
    {
        return $this->get_template_dir() . $this->get_path();
    }

    /**
     * -----------------------
     *   Q U E R Y   V A R S
     * -----------------------
     */

    /**
     * Remove query vars
     */
    private function remove_query_vars() : void
    {
        $this->set_query_vars( false );
    }

    /**
     * Set query vars for extraction
     */
    private function set_query_vars( bool $to_set = true ) : void
    {
        foreach ( $this->get_vars() as $key => $value )
        {
            set_query_var(
                $key,
                $to_set ? $value : null
            );
        }
    }

    /**
     * -------------
     *   P R O P S
     * -------------
     */

    /**
     * Get path relative to template directory
     */
    private function get_path() : string
    {
        return $this->get_prop( 'path' );
    }

    /**
     * Get path to template directory
     */
    private function get_template_dir() : string
    {
        return $this->get_prop( 'base_dir' );
    }

    /**
     * Get variables to pass to the template
     */
    private function get_vars() : array
    {
        return $this->get_prop( 'vars' );
    }

    /**
     * Check whether to prevent repeat includes
     */
    private function require_once() : bool
    {
        return boolval( $this->get_prop( 'require_once' ) );
    }

}   // End of class