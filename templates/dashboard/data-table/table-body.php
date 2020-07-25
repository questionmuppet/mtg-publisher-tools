<?php
/**
 * Data-table body section
 * 
 * @param Table_Data $table_data
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

?>

<?php foreach ( $table_data->get_rows() as $row ) : ?>

    <tr>
        <?php foreach( $table_data->get_fields() as $field )
        {
            $field->print_body_cell( $row );
        } ?>
    </tr>

<?php endforeach; ?>