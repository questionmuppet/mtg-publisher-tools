<?php
/**
 * Updates dashboard tab
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

// Check permissions
current_user_can( 'manage_options' ) or die("Quit 'yer sneakin around!");

$post_url = admin_url( 'admin-post.php' );

$updates_available = isset( $_GET['out-of-date'] );
$update_text = $updates_available
    ? '<span style="color: red;">New Magic card data is available for download.</span>'
    : 'No updates are currently pending.';

if ( 'checked_available' === $action )
{
    $params = [
        'type' => 'warning',
        'message' => 'New updates are available to the Magic card data used by MTG Publisher Tools. Click "Update" to install the latest changes.',
    ];
}
if ( 'checked_current' === $action )
{
    $params = [
        'type' => 'warning',
        'message' => 'Your Magic card data for MTG Publisher Tools is already up to date.',
    ];
}
if ( 'updated' === $action )
{
    $params = [
        'type' => 'success',
        'message' => 'Your Magic card data for MTG Publisher Tools has been updated to the latest version.',
    ];
}

if ( isset( $params ) )
{
    $notice = Mtgtools\Mtgtools_Plugin::get_instance()->wp_tasks()->create_admin_notice( $params );
    $notice->print();
}

?>

<section>

    <h2>Database Updates</h2>

    <p>MTG Publisher Tools is set to check for new updates <strong>weekly</strong>. To change this behavior, go to the <a href="<?php echo esc_url( $dashboard->get_tab_url('settings') ); ?>">Settings</a> page.</p>

    <?php $dashboard->print_info_table([
        'Source'       => $updates->get_source_name(),
        'Link'         => $updates->get_source_url(),
        'Last checked' => $updates->get_last_checked(),
        'Status'       => $updates->get_update_status(),
    ]); ?>

    <form action="<?php echo esc_url( $post_url ); ?>" class="inline-form" method="POST">

        <?php $dashboard->print_action_inputs([
            'action' => 'mtgtools_update_symbols',
            'label' => 'Update',
        ]); ?>

    </form>
    
    <form action="<?php echo esc_url( $post_url ); ?>" class="inline-form" method="POST">
        
        <?php $dashboard->print_action_inputs([
            'action' => 'mtgtools_check_updates',
            'label' => 'Check for updates',
        ]); ?>

    </form>

</section>