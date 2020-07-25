<?php
/**
 * Data table for display on admin
 * 
 * @param Dashboard_Tab $active_tab
 * @param Table_Data $table_data
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

?>

<div class="mtgtools-table-wrapper" >
    
    <form class="mtgtools-table-controls">
        
        <label>Filter <input
            class="mtgtools-table-filter-input"
            type="text"
            data-dashboard_tab="<?php echo esc_attr( $active_tab->get_id() ); ?>"
            data-table_key="<?php echo esc_attr( $table_data->get_key() ); ?>"
        /></label>

    </form>

    <div class="mtgtools-table-scroll-wrapper">

        <table class="mtgtools-table">

            <thead class="mtgtools-table-head">

                <tr>
                    <?php foreach ( $table_data->get_fields() as $field )
                    {
                        $field->print_header_cell();
                    } ?>
                </tr>

            </thead>

            <tbody class="mtgtools-table-body">

                <?php load_template( dirname( __FILE__ ) . '/table-body.php', false ); ?>

            </tbody>

        </table>

    </div>

</div>