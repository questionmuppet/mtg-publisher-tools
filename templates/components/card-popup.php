<?php
/**
 * Magic card popup for use in hover-over links
 * 
 * @param Magic_Card $card      Magic card to use for content
 * @param Image_Uri $image      Image to use for content
 * @param string $tooltip       Tooltip location: "left|right|top|bottom"
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

$classes = array_filter([
    'mtgtools-card-popup',
    'hidden',
    'mtgtools-tooltip',
    $tooltip,
]);

?>

<span class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

    <img
        class="mtgtools-card-image"
        src="<?php echo esc_url( $image->get_uri() ); ?>"
        alt="<?php echo esc_attr( $card->get_name_with_edition() ); ?>"
    />

</span>