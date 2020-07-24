<?php
/**
 * Mtgtools_Images
 * 
 * Module for downloading, cacheing, and outputting card images
 */

namespace Mtgtools;

use Mtgtools\Abstracts\Module;
use Mtgtools\Cards;

use Mtgtools\Exceptions\Mtg;
use Mtgtools\Exceptions\Admin_Post;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Mtgtools_Images extends Module
{
    /**
     * Search filters
     */
    private $search_filters = [
        'id',
        'backface',
        'name',
        'set',
        'number',
        'language'
    ];

    /**
     * Parameters
     */
    private $default_image_type;

    /**
     * Dependencies
     */
    private $card_cache;

    /**
     * Constructor
     */
    public function __construct( Cards\Card_Cache $card_cache, string $img_type, $plugin )
    {
        $this->card_cache = $card_cache;
        $this->default_image_type = $img_type;
        parent::__construct( $plugin );
    }

    /**
     * Add WordPress hooks
     */
    public function add_hooks() : void
    {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
        add_shortcode( 'mtg_card', array( $this, 'add_card_link' ) );
        $this->register_post_handlers([
            [
                'type' => 'ajax',
                'action' => 'mtgtools_get_card_popup',
                'callback' => array( $this, 'get_popup_markup' ),
                'user_args' => $this->get_valid_search_filters(),
                'nopriv' => true,
                'capability' => 'read',
            ]
        ]);
    }

    /**
     * Enqueue assets for shortcode
     */
    public function enqueue() : void
    {
        if ( $this->get_plugin_option( 'enable_card_popups' ) )
        {
            $this->add_style([
                'key' => 'mtgtools-card-links',
                'path' => 'card-links.css',
            ]);
            $this->add_script([
                'key' => 'mtgtools-card-links',
                'path' => 'card-links.js',
                'deps' => array('jquery'),
                'data' => [
                    'mtgtoolsCardLinkData' => [
                        'ajaxurl' => admin_url( 'admin-ajax.php' ),
                        'nonce' => wp_create_nonce('mtgtools_get_card_popup'),
                    ]
                ],
            ]);
        }
    }

    /**
     * -------------------------------
     *   L I N K   S H O R T C O D E
     * -------------------------------
     */

    /**
     * Add card link
     * 
     * @param array $atts   Optional list of search criteria
     * @return string       Content wrapped in link
     */
    public function add_card_link( $atts, $content = null ) : string
    {
        $link = new Cards\Card_Link([
            'content' => $content,
            'filters' => array_filter(
                shortcode_atts(
                    $this->get_shortcode_defaults(),
                    $atts
                )
            ),
        ]);
        if ( !$this->fetching_lazily() )
        {
            $uri = $this->get_image_uri( $link->get_filters(), $this->get_default_image_type() );
            $link->set_href( $uri );
        }
        return $link->get_markup();
    }

    /**
     * Get shortcode defaults
     */
    private function get_shortcode_defaults() : array
    {
        return array_fill_keys( $this->get_valid_search_filters(), '' );
    }

    /**
     * -------------
     *   P O P U P
     * -------------
     */

    /**
     * Get HTML for a card popup
     * 
     * @param array $filters Search criteris
     * @throws PostHandlerException
     */
    public function get_popup_markup( array $filters ) : array
    {
        try
        {
            $type = $this->get_default_image_type();
            $card = $this->get_magic_card( $filters, $type );
            $image = $card->get_image( $type );

            $template = $this->wp_tasks()->create_template([
                'path' => 'components/card-popup.php',
                'vars' => [
                    'card' => $card,
                    'image' => $image,
                    'tooltip' => $this->get_tooltip_location(),
                ],
            ]);
            
            return [
                'transients' => [ 'popup' => $template->get_markup() ],
                'href' => $image->get_uri(),
            ];
        }
        catch ( Mtg\MtgDataException $e )
        {
            $exception = ( $e instanceof Mtg\MtgParameterException )
                ? new Admin_Post\ParameterException( "Failed to generate an image popup due to a bad request. Your search terms must adhere to a valid search scheme.", 0, $e )
                : new Admin_Post\ExternalCallException( "Failed to generate an image popup. No Magic card could be located matching the specified filters.", 0, $e );
            throw $exception;
        }
    }

    /**
     * -------------------
     *   M T G   D A T A
     * -------------------
     */

    /**
     * Get an image uri
     * 
     * @return string Uri to remote image, empty string if not found
     */
    private function get_image_uri( array $filters, string $type ) : string
    {
        try
        {
            $card = $this->get_magic_card( $filter, $type );
            return $card->get_image( $type )->get_uri();
        }
        catch ( Mtg\MtgDataException $e )
        {
            return '';
        }
    }

    /**
     * Get a Magic card from the cache
     * 
     * @param array $filters                Search terms for the card
     * @param string $preferred_image_type  Image type to fetch if missing
     * @throws MtgDataException
     */
    private function get_magic_card( array $filters, string $preferred_image_type ) : Cards\Magic_Card
    {
        return $this->card_cache()->locate_card( $filters, $preferred_image_type );
    }
    
    /**
     * -----------------
     *   O P T I O N S
     * -----------------
     */
    
    /**
     * Get valid search filters
     */
    private function get_valid_search_filters() : array
    {
        return $this->search_filters;
    }
    
    /**
     * Get default image type
     */
    private function get_default_image_type() : string
    {
        return $this->default_image_type;
    }

    /**
     * Check for lazy fetch
     */
    private function fetching_lazily() : bool
    {
        return $this->get_plugin_option( 'lazy_fetch_images' );
    }

    /**
     * Get popup tooltip location
     */
    private function get_tooltip_location() : string
    {
        return $this->get_plugin_option( 'popup_tooltip_location' );
    }

    /**
     * ---------------------------
     *   D E P E N D E N C I E S
     * ---------------------------
     */

    /**
     * Get card cache
     */
    private function card_cache() : Cards\Card_Cache
    {
        return $this->card_cache;
    }

}   // End of class