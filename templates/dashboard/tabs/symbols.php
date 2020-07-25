<?php
/**
 * Mana symbols dashboard tab
 * 
 * @param Mtgtools_Dashboard $dashboard
 */

// Exit if accessed directly
defined( 'MTGTOOLS__PATH' ) or die("Don't mess with it!");

// Check permissions
current_user_can( 'manage_options' ) or die("Quit 'yer sneakin around!");

?>

<section class="mtgtools-flex horiz">
    
    <div class="mtgtools-information-panel mtgtools-flex-item">
        
        <h2>Using Mana Symbols</h2>

        <p>Mana symbols can be included in posts and themes using the <code>[mana_symbols]</code> shortcode. Wrap whatever text you want to parse within the shortcode tags, and WordPress handles the rest. You can also show a single symbol with the alternate shortcode <code>[mana_symbol]</code>.</p>

        <p>By default MTG Publisher Tools pulls its data from Scryfall. Both official Oracle text and text copied from Scryfall should be parsable by <code>[mana_symbol]</code> tags. When in doubt, refer to the table on this page for the correct code.</p>

        <?php $examples = [
            '[mana_symbol key="{E}"]',
            '[mana_symbols]{T}: Add {G}{G}.[/mana_symbols]',
            '[mana_symbols]Pay {X}{R}{R}: Deal X damage.[/mana_symbols]',
        ]; ?>
        <?php $dashboard->print_simple_table([
            'columns' => [ 'Example', 'Output' ],
            'rows' => [
                [ "<code>{$examples[0]}</code>", do_shortcode( $examples[0] ) ],
                [ "<code>{$examples[1]}</code>", do_shortcode( $examples[1] ) ],
                [ "<code>{$examples[2]}</code>", do_shortcode( $examples[2] ) ],
            ],
        ]); ?>

        <hr width="50%">
        
        <h2>Toolbar Button</h2>
        
        <p>Within Classic blocks and the Classic Editor, mana-symbol tags can be inserted using the toolbar. To do this, highlight the text you want to wrap and click the mana symbols button.</p>

        <img
            src="<?php echo esc_url( MTGTOOLS__ASSETS_URL . 'img/mana-symbol-tags-toolbar.png' ); ?>"
            alt="Toolbar with mana symbol button selected"
            class="mtgtools-screenshot"
        />
        
    </div>
    
    <div class="mtgtools-flex-item" style="margin-top: 26px;">
    
        <?php $dashboard->display_table( 'symbol_list' ); ?>
    
    </div>

</section>