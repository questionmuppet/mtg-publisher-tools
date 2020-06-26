<?php
/**
 * Data table for display on admin
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

// Check permissions
current_user_can( 'manage_options' ) or die("Quit 'yer sneakin around!");

?>

<table class="mtgtools-admin-table">

    <thead class="mtgtools-admin-table head">

        <tr>
            <?php foreach ( $table_data->get_fields() as $field )
            {
                $field->print_header_cell();
            } ?>
        </tr>

    </thead>

    <tbody class="mtgtools-admin-table body">
        
        <?php foreach ( $table_data->get_rows() as $row ) : ?>

            <tr>
                <?php foreach( $table_data->get_fields() as $field )
                {
                    $field->print_body_cell( $row );
                } ?>
            </tr>

        <?php endforeach; ?>

    </tbody>

</table>