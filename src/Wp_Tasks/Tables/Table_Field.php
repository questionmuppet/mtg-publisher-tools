<?php
/**
 * Table_Field
 * 
 * Defines a column for use in tables
 */

namespace Mtgtools\Wp_Tasks\Tables;
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
        'width' => 'auto',
    );

    /**
     * Print <th> header cell
     */
    public function print_header_cell() : void
    {
        printf(
            '<th class="%s" width="%s">%s</th>',
            esc_attr( $this->get_css_class() ),
            esc_attr( $this->get_cell_width() ),
            esc_html( $this->get_title() )
        );
    }

    /**
     * Print <td> body cell
     */
    public function print_body_cell( array $row ) : void
    {
        printf(
            '<td class="%s" width="%s">%s</td>',
            esc_attr( $this->get_css_class() ),
            esc_attr( $this->get_cell_width() ),
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
     * Get cell width
     */
    private function get_cell_width() : string
    {
        return strval( $this->get_prop( 'width' ) );
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