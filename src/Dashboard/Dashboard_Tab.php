<?php
/**
 * Dashboard_Tab
 * 
 * Tabbed section of the admin dashboard
 */

namespace Mtgtools\Dashboard;
use Mtgtools\Abstracts\Data;
use Mtgtools\Mtgtools_Enqueue;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Dashboard_Tab extends Data
{
    /**
     * Required properties
     */
    protected $required = array( 'id' );

    /**
     * Default properties
     */
    protected $defaults = array(
        'title'   => null,
        'scripts' => [],
        'styles'  => [],
    );

    /**
     * Enqueue JS and CSS assets
     */
    public function enqueue_assets( Mtgtools_Enqueue $enqueue ) : void
    {
        foreach ( $this->get_script_defs() as $params )
        {
            $enqueue->add_script( $params );
        }
        foreach ( $this->get_style_defs() as $params )
        {
            $enqueue->add_style( $params );
        }
    }

    /**
     * Output navigation tab HTML
     */
    public function output_nav_tab( string $active_tab )
    {
        printf(
            '<a href="%s" class="%s">%s</a>',
            esc_url( $this->get_href() ),
            esc_attr( $this->get_css_class( $active_tab === $this->get_id() ) ),
            esc_html( $this->get_title() )
        );
    }

    /**
     * Get href
     */
    public function get_href() : string
    {
        return add_query_arg(
            [
                'page' => MTGTOOLS__ADMIN_SLUG,
                'tab' => sanitize_key( $this->get_id() ),
            ],
            admin_url( "options-general.php" )
        );
    }

    /**
     * Get CSS class string
     */
    protected function get_css_class( bool $is_active ) : string
    {
        $classes = array_filter([
            'nav-tab',
            $is_active ? 'nav-tab-active' : '',
        ]);
        return implode( ' ', $classes );
    }

    /**
     * Get title
     */
    protected function get_title() : string
    {
        return $this->get_prop( 'title' ) ?? ucfirst( $this->get_id() );
    }

    /**
     * Get parameters for JS assets
     */
    private function get_script_defs() : array
    {
        return $this->get_prop( 'scripts' );
    }

    /**
     * Get parameters for CSS assets
     */
    private function get_style_defs() : array
    {
        return $this->get_prop( 'styles' );
    }

    /**
     * Get id
     */
    public function get_id() : string
    {
        return $this->get_prop( 'id' );
    }

}   // End of class