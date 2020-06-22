<?php
/**
 * Admin dashboard page
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

// Check permissions
current_user_can( 'manage_options' ) or die("Quit 'yer sneakin around!");

// Dashboard module
$Mtgtools_Dashboard = get_query_var( 'Mtgtools_Dashboard' );

// Currently selected tab
$active = $Mtgtools_Dashboard->get_active_tab()->get_id();

?>

<div class="wrap">
	
	<h1>MTG Publisher Tools</h1>

    <nav class="nav-tab-wrapper">

        <?php foreach( $Mtgtools_Dashboard->get_tabs() as $tab )
        {
            $tab->output_nav_tab( $active );
        } ?>

    </nav>

    <?php load_template( dirname( __FILE__ ) . "/tabs/{$active}.php" ); ?>

</div>