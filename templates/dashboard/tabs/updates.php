<?php
/**
 * Dashboard Tab: Updates
 * 
 * Checks for and installs database updates
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

// Check permissions
current_user_can( 'manage_options' ) or die("Quit 'yer sneakin around!");

/**
 * Get data for template
 */
$post_url = admin_url( 'admin-post.php' );
$updates = Mtgtools\Mtgtools_Plugin::get_instance()->updates();

/**
 * Print notices from a completed admin-post action
 */
$dashboard->print_action_notices([
    'failed' => [
        'title' => 'Failed Connection',
        'type' => 'error',
        'message' => 'MTG Publisher Tools encountered an error trying to connect to an external data source. Troubleshooting steps:',
        'list' => [
            'Check your connection and try again.',
            "Check the status of your data provider: {$updates->get_nice_source_link()}.",
            'Contact the site administrator.',
        ],
    ],
    'checked_available' => [
        'type' => 'warning',
        'message' => '<strong>Updates available.</strong> New updates are available to the Magic card data used by MTG Publisher Tools. Click "Update" below to install the latest changes.',
    ],
    'checked_current' => [
        'type' => 'warning',
        'message' => '<strong>No updates needed.</strong> Your Magic card data for MTG Publisher Tools is already synced to the latest version.',
    ],
    'updated' => [
        'type' => 'success',
        'message' => '<strong>Updated.</strong> Your Magic card data for MTG Publisher Tools has been updated to the latest version.',
    ],
]);

?>

<section>

    <h2>Database Updates</h2>

    <p>MTG Publisher Tools is set to check for new updates <strong>weekly</strong>. To change this behavior, go to the <a href="<?php echo esc_url( $dashboard->get_tab_url('settings') ); ?>">Settings</a> page.</p>

    <?php $dashboard->print_info_table( $updates->get_status_info() ); ?>

    <form action="<?php echo esc_url( $post_url ); ?>" class="inline-form" method="POST">

        <?php $dashboard->print_action_inputs([
            'action' => 'mtgtools_update_symbols',
            'label' => 'Update',
            'primary' => true,
        ]); ?>

    </form>
    
    <form action="<?php echo esc_url( $post_url ); ?>" class="inline-form" method="POST">
        
        <?php $dashboard->print_action_inputs([
            'action' => 'mtgtools_check_updates',
            'label' => 'Check for updates',
        ]); ?>

    </form>

</section>