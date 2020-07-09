<?php
/**
 * Component: Info Table
 * 
 * Prints a simple table for display of static information. Basic
 * markup is allowed in header and cell values
 * 
 * @param array $rows     Associative array of "header" to [cell values]
 * @param array $classes  Additional classes to add to main <table> element
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

// Compile CSS classes
$classes[] = 'info-table';
$css_class = implode( ' ', $classes );

?>

<table class="<?php echo esc_attr( $css_class ); ?>">

    <tbody class="info-table-body">

        <?php foreach ( $rows as $header => $cells ) : ?>

            <tr class="info-table-row">

                <th class="info-table-cell"><?php echo wp_kses_post( $header ); ?></th>

                <?php foreach ( (array) $cells as $value ) : ?>

                    <td class="info-table-cell"><?php echo wp_kses_post( $value ); ?></td>

                <?php endforeach; ?>

            </tr>

        <?php endforeach; ?>

    </tbody>

</table>