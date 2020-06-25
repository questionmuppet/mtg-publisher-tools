<?php
/**
 * Table_Field
 * 
 * Defines a column for use in tables
 */

namespace Mtgtools\Dashboard;
use Mtgtools\Abstracts\Data;

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

class Table_Field extends Data
{
    /**
     * Required properties
     */
    protected $required = array( 'id' );

    /**
     * Default properties
     */
    protected $defaults = array(
        'title' => null,
    );

    /**
     * Print <th> header cell
     */
    public function print_header_cell() : void
    {
        printf(
            '<th class="%s">%s</th>',
            esc_attr( $this->get_css_class() ),
            esc_html( $this->get_title() )
        );
    }

    /**
     * Print <td> body cell
     */
    public function print_body_cell( array $row ) : void
    {
        printf(
            '<td class="%s">%s</td>',
            esc_attr( $this->get_css_class() ),
            wp_kses_post( $this->find_content( $row ) )
        );
    }

    /**
     * Get CSS class string
     */
    private function get_css_class() : string
    {
        $classes = [
            'mtgtools-table-cell',
            $this->get_key(),
        ];
        return implode( ' ', $classes );
    }

    /**
     * Get title
     */
    private function get_title() : string
    {
        return $this->get_prop( 'title' ) ?? ucfirst( $this->get_key() );
    }

    /**
     * Find content in row data
     */
    private function find_content( array $row ) : string
    {
        return $row[ $this->get_key() ] ?? '';
    }

    /**
     * Get field key
     */
    private function get_key() : string
    {
        return $this->get_prop( 'id' );
    }

}   // End of class