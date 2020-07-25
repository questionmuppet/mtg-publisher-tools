<?php
/**
 * Admin dashboard page
 * 
 * @param Mtgtools_Dashboard $dashboard
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

// Check permissions
current_user_can( 'manage_options' ) or die("Quit 'yer sneakin around!");

// Selected tab key
$active = $dashboard->get_active_tab()->get_id();

?>

<div class="wrap">
	
	<h1>MTG Publisher Tools</h1>

    <nav class="nav-tab-wrapper">

        <?php foreach( $dashboard->get_tabs() as $tab )
        {
            $tab->output_nav_tab( $active );
        } ?>

    </nav>

    <?php load_template( dirname( __FILE__ ) . "/tabs/{$active}.php" ); ?>

</div>