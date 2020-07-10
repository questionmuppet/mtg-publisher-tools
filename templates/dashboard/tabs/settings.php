<?php
/**
 * Settings dashboard tab
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

// Check permissions
current_user_can( 'manage_options' ) or die("Quit 'yer sneakin around!");

?>

<section>
    
    <form action="options.php" method="post" id="mtgtools_settings_form" >

        <?php
        
        settings_fields( MTGTOOLS__ADMIN_SLUG . '_settings' );         // Nonces, etc.
        do_settings_sections( MTGTOOLS__ADMIN_SLUG . '_settings' );    // Form controls
        submit_button();
        
        ?>

    </form>

</section>