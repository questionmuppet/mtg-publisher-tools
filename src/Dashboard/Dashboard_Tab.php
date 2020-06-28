<?php
/**
 * Dashboard_Tab
 * 
 * Tabbed section of the admin dashboard
 */

namespace Mtgtools\Dashboard;
use Mtgtools\Abstracts\Data;
use Mtgtools\Enqueue\Asset;
use Mtgtools\Dashboard\Table_Data;

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
        'title'  => null,
        'assets' => [],
        'tables' => [],
    );

    /**
     * Constructor
     */
    public function __construct( array $props = [] )
    {
        parent::__construct( $props );
        foreach ( $this->get_assets() as $asset )
        {
            if ( !$asset instanceof Asset )
            {
                throw new \InvalidArgumentException( "Tried to instantiate a " . get_called_class() . " using invalid asset data. Asset parameters passed to dash tabs must be instances of Enqueue\Asset." );
            }
        }
        foreach ( $this->get_tables() as $table )
        {
            if ( !$table instanceof Table_Data )
            {
                throw new \InvalidArgumentException( "Tried to instantiate a " . get_called_class() . " using invalid table data. Table parameters passed to dash tabs must be instances of Dashboard\Table_Data" );
            }
        }
    }

    /**
     * Enqueue JS and CSS assets
     */
    public function enqueue_assets() : void
    {
        foreach ( $this->get_assets() as $asset )
        {
            $asset->enqueue();
        }
    }
    
    /**
     * Get table data by key
     */
    public function get_table_data( string $key ) : Table_Data
    {
        if ( !$this->table_exists( $key ) )
        {
            throw new \OutOfRangeException( get_called_class() . " tried to retrieve an undefined Table_Data object with key '{$key}'." );
        }
        return $this->get_tables()[ $key ];
    }

    /**
     * -------------------------
     *   H T M L   O U T P U T
     * -------------------------
     */

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
    private function get_css_class( bool $is_active ) : string
    {
        $classes = array_filter([
            'nav-tab',
            $is_active ? 'nav-tab-active' : '',
        ]);
        return implode( ' ', $classes );
    }

    /**
     * -----------------------
     *   P R O P E R T I E S
     * -----------------------
     */

    /**
     * Get title
     */
    private function get_title() : string
    {
        return $this->get_prop( 'title' ) ?? ucfirst( $this->get_id() );
    }

    /**
     * Get JS and CSS assets
     * 
     * @return Asset[]
     */
    private function get_assets() : array
    {
        return $this->get_prop( 'assets' );
    }

    /**
     * Check if table is defined
     */
    private function table_exists( string $key ) : bool
    {
        return array_key_exists( $key, $this->get_tables() );
    }

    /**
     * Get defined data tables
     */
    private function get_tables() : array
    {
        return $this->get_prop( 'tables' );
    }

    /**
     * Get id
     */
    public function get_id() : string
    {
        return $this->get_prop( 'id' );
    }

}   // End of class