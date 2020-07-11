<?php
/**
 * Component: Action Inputs
 * 
 * Prints submit button and hidden inputs required for an admin-post action
 * 
 * @param string $action    Admin-post action key
 * @param string $label     Label for the submit button
 * @param bool $primary     Whether to style button as WordPress primary action
 * @param bool $disabled    Action and concomitant button are disabled
 * 
 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/admin_post_(action)
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

$nonce = wp_create_nonce( $action );
$button_class = $primary && !$disabled
    ? 'button button-primary'
    : 'button';

?>

<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>" />
<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />

<button

    type="submit"
    class="<?php echo esc_attr( $button_class ); ?>"
    <?php echo $disabled ? ' disabled="disabled"' : '' ?>

><?php echo esc_html( $label ); ?></button>