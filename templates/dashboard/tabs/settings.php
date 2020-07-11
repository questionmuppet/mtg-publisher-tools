<?php
/**
 * Settings dashboard tab
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

// Check permissions
current_user_can( 'manage_options' ) or die("Quit 'yer sneakin around!");

/**
 * Admin-post action notices
 */
$dashboard->print_action_notices([
    'notices_disabled' => [
        'type' => 'success',
        'message' => sprintf(
            '<strong>Notices disabled.</strong> MTG Publisher Tools will no longer show notices about pending updates to administrators. To turn notices back on, go to the <a href="%s">Settings</a> page.',
            esc_url( $dashboard->get_tab_url('settings') )
        ),
    ],
]);

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