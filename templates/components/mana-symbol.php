<?php
/**
 * Outputs HTML markup for mana symbols
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

?>

<abbr class="mtg-abbreviation" title="<?php echo esc_attr( $symbol->get_english_phrase() ); ?>">

    <img
        class="<?php echo esc_attr( $symbol->get_css_class() ); ?>"
        alt="<?php echo esc_attr( $symbol->get_english_phrase() ); ?>"
        src="<?php echo esc_attr( $symbol->get_svg_uri() ); ?>"
    />

</abbr>