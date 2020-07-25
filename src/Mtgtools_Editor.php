<?php
/**
 * Mtgtools_Editor
 * 
 * Adds functionality to the Gutenberg and Classic editors for inserting MTG content
 */

namespace Mtgtools;
use Mtgtools\Abstracts\Module;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Editor extends Module
{
    /**
     * Add WordPress hooks
     */
    public function add_hooks() : void
    {
        add_filter( 'mce_buttons',          array( $this, 'register_buttons' ) );
        add_filter( 'mce_external_plugins', array( $this, 'add_mce_plugin' ) );
    }

    /**
     * Register tinyMCE buttons
     */
    public function register_buttons( array $buttons )
    {
        $buttons[] = 'add_mtg_symbols';
        $buttons[] = 'add_mtg_card_link';
        return $buttons;
    }

    /**
     * Create tinyMCE plugin
     */
    public function add_mce_plugin( array $plugin_array )
    {
        $plugin_array['mtg_publisher_tools'] = MTGTOOLS__ASSETS_URL . 'tinymce/mtgtools-plugin.js';
        return $plugin_array;
    }

}   // End of class