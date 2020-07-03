<?php
/**
 * Admin-Notice: Update availability
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

$update_link = add_query_arg(
    [
        'action' => 'mtgtools_update_symbols',
        '_wpnonce' => wp_create_nonce( 'mtgtools_update_symbols' ),
    ],
    admin_url( 'admin-post.php' )
);

?>

<p>The mana symbols used in your posts and themes have new updates available for download. To sync the MTG Publisher Tools database to the latest changes, click "Update now".</p>

<p><a href="<?php echo esc_url( $update_link ); ?>" class="button button-primary mtgtools-notice-button">Update now</a> <a href="" class="button">Turn off notices</a></p>