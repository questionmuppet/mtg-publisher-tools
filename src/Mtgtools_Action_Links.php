<?php
/**
 * Mtgtools_Action_Links
 * 
 * Adds action links to the plugins page
 */

namespace Mtgtools;
use Mtgtools\Abstracts\Module;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Action_Links extends Module
{
    /**
     * Add WordPress hooks
     */
    public function add_hooks() : void
    {
        add_filter( 'plugin_action_links_' . MTGTOOLS__BASENAME, array( $this, 'add_action_links' ) );
    }
    
	/**
	 * Create plugin action links
	 */
	public function add_action_links( array $actions ) : array
	{
		return array_merge( $this->get_link_markups(), $actions );
    }

    /**
     * Get array of links as HTML markup
     */
    private function get_link_markups() : array
    {
        $links = [];
        foreach ( $this->get_links() as $label => $href )
        {
            $links[] = sprintf(
                '<a href="%s">%s</a>',
                esc_url( $href ),
                esc_html( $label )
            );
        }
        return $links;
    }
    
    /**
     * Get plugin links
     */
    private function get_links() : array
    {
        return [
            'Updates' => $this->get_dashboard_url('updates'),
            'Settings' => $this->get_dashboard_url('settings'),
        ];
    }

}   // End of class