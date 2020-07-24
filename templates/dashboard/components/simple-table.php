<?php
/**
 * Component: Simple Table
 * 
 * Prints a static table with a row of header cells at the top.
 * Basic markup is allowed in cell values.
 * 
 * @param string[] $columns     One or more column names
 * @param array[] $rows         Array of arrays with cell data
 * @param array $classes        Additional classes to add to main <table> element
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

// Compile CSS classes
$classes[] = 'info-table';
$css_class = implode( ' ', $classes );

?>

<table class="<?php echo esc_attr( $css_class ); ?>">

    <thead class="info-table-head">
        <tr class="info-table-row">
            <?php foreach ( $columns as $name ) : ?>
                <th class="info-table-cell"><?php echo wp_kses_post( $name ); ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    
    <tbody class="info-table-body">
        <?php foreach ( $rows as $cells ) : ?>
            <tr class="info-table-row">
                <?php foreach ( (array) $cells as $value ) : ?>
                    <td class="info-table-cell"><?php echo wp_kses_post( $value ); ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>

</table>