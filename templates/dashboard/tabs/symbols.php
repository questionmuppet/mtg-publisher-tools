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

        <p>Mana symbols can be inserted into posts and themes using the <code>[oracle_text]</code> and <code>[mana_symbol]</code> shortcodes.</p>
        
        <p><code>[oracle_text]</code> is designed to render entire passages like they would appear on cards. Simply wrap whatever text you want to parse within the shortcode tags, and WordPress handles the rest.</p>
        
        <p>To insert a single mana symbol, use the <code>[mana_symbol]</code> shortcode. This shortcode takes one argument, <code>key</code>, containing the plaintext code of the symbol.</p>

        <?php $examples = [
            '[mana_symbol key="{E}"]',
            '[oracle_text]{T}: Add {G}{G}.[/oracle_text]',
            '[oracle_text]Pay {X}{R}{R}: Deal X damage.[/oracle_text]',
        ]; ?>
        <?php $dashboard->print_simple_table([
            'columns' => [ 'Example', 'Output' ],
            'rows' => [
                [ "<code>{$examples[0]}</code>", do_shortcode( $examples[0] ) ],
                [ "<code>{$examples[1]}</code>", do_shortcode( $examples[1] ) ],
                [ "<code>{$examples[2]}</code>", do_shortcode( $examples[2] ) ],
            ],
        ]); ?>
        
        <p>Plaintext representations of mana symbols are based on WotC's official Oracle text notation. You can read more about this in the <a href="http://magic.wizards.com/en/game-info/gameplay/rules-and-formats/rules" target="_blank">Comprehensive Rules</a>. When in doubt, refer to the table on this page for the correct code.</p>

        <hr width="50%">
        
        <h2>Toolbar Button</h2>
        
        <p>Within Classic blocks and the Classic Editor, <code>[oracle_text]</code> tags can be inserted using the toolbar. To do this, highlight the text you want to wrap and click the button with a colorless mana symbol.</p>

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