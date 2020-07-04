<?php
/**
 * Updates dashboard tab
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

// Check permissions
current_user_can( 'manage_options' ) or die("Quit 'yer sneakin around!");

$action = $_GET['action'] ?? '';
$updates_available = isset( $_GET['out-of-date'] );
$update_text = $updates_available
    ? '<span style="color: red;">New Magic card data is available for download.</span>'
    : 'No updates are currently pending.';
$update_nonce = wp_create_nonce( 'mtgtools_update_symbols' );
$check_nonce = wp_create_nonce( 'mtgtools_check_updates' );

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

    <table style="margin: 1em 0;">
        <cols></cols>
        <tbody>
            <tr>
                <th style="text-align: left; padding-right: 1em;">Source</th>
                <td>Scryfall API</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-right: 1em;">Link</th>
                <td><a href="https://scryfall.com/docs/api/" target="_blank">https://scryfall.com/docs/api/</a></td>
            </tr>
            <tr>
                <th style="text-align: left; padding-right: 1em;">Last checked</th>
                <td>June 20, 1081 23:38:09</td>
            </tr>
            <tr>
                <th style="text-align: left; padding-right: 1em;">Status</th>
                <td style="font-style: italic;"><?php echo $update_text; ?></td>
            </tr>
        </tbody>
    </table>

    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" style="display: inline;">

        <input type="hidden" name="action" value="mtgtools_update_symbols" />
        <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $update_nonce ); ?>" />
        <button type="submit" class="button button-primary">Update</button>

    </form>
    
    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" style="display: inline;">

        <input type="hidden" name="action" value="mtgtools_check_updates" />
        <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $check_nonce ); ?>" />
        <button type="submit" class="button">Check for updates</button>

    </form>

</section>