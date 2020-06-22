<?php
/**
 * Scryfall_List_Paginator
 * 
 * Fetches a paginated list from the Scryfall API
 */

namespace Mtgtools\Scryfall\Requests;
use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Scryfall_List_Paginator extends Data
{
    /**
     * Cached results
     */
    private $pages = [];

    /**
     * Cursor
     */
    private $cursor = 0;

    /**
     * Total item count reported by API
     */
    private $total;

    /**
     * Tracks whether there's more data to fetch
     */
    private $has_more = true;

    /**
     * Next page to fetch
     */
    private $next_page;

    /**
     * Required properties
     */
    protected $required = array( 'endpoint' );

    /**
     * -----------------------------
     *   P U B L I C   A C C E S S
     * -----------------------------
     */

    /**
     * Get full list of all items
     */
    public function get_full_list() : array
    {
        while ( $this->has_more() )
        {
            $this->fetch_next_page();
            $this->cursor = count( $this->pages );
        }
        return array_merge( ...array_values( $this->pages ) );   // Unzip into one array
    }
    
    /**
     * Check if more results are available to fetch
     */
    public function has_more() : bool
    {
        return $this->has_more;
    }

    /**
     * Get next page of items and advance cursor
     */
    public function get_next_page() : array
    {
        while ( $this->cursor >= count( $this->pages ) )
        {
            if ( !$this->has_more() )
            {
                throw new \LogicException( "Tried to fetch the next page in a completed list. Make sure to call 'has_more()' before fetching a page, or use 'get_full_list()' to fetch all at once." );
            }
            $this->fetch_next_page();
        }
        return $this->pages[ $this->cursor++ ];
    }

    /**
     * Get total count
     */
    public function get_total_count() : int
    {
        if ( !$this->initial_page_fetched() )
        {
            $this->fetch_next_page();
        }
        return $this->total;
    }

    /**
     * -------------------------------
     *   F E T C H   F R O M   A P I
     * -------------------------------
     */

    /**
     * Fetch next available page
     */
    private function fetch_next_page() : void
    {
        $params = array_filter([
            'method'   => 'GET',
            'expects'  => 'list',
            'endpoint' => $this->initial_page_fetched() ? '' : $this->get_endpoint(),
            'full_url' => $this->next_page ?? '',
        ]);
        $request = new Scryfall_Request( $params );
        $this->cache_new_page( $request->get_data() );
    }
    
    /**
     * Cache a new page and advance page tracker
     */
    private function cache_new_page( array $response ) : void
    {
        if ( !isset( $this->total ) )
        {
            $this->total = $this->find_total( $response );
        }
        $this->pages[] = $response['data'];
        $this->advance_page_tracker( $response );
    }
    
    /**
     * Advance internal page tracker
     */
    private function advance_page_tracker( array $response ) : void
    {
        $this->has_more = boolval( $response['has_more'] );
        $this->next_page = $response['next_page'] ?? null;
    }

    /**
     * Find total in response data
     */
    private function find_total( array $response ) : int
    {
        $matches = preg_grep( '/^total_[a-zA-Z]+$/', array_keys( $response ) );
        $key = array_shift( $matches );
        return intval(
            $response[ $key ]
            ?? count( $response['data'] )
        );
    }

    /**
     * Check if the first page has been fetched
     */
    private function initial_page_fetched() : bool
    {
        return boolval( count( $this->pages ) );
    }
    
    /**
     * Get endpoint
     */
    private function get_endpoint() : string
    {
        return $this->get_prop( 'endpoint' );
    }

}   // End of class