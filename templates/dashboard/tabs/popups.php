<?php
/**
 * Card popups dashboard tab
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
        
        <h2>Using Card Popups</h2>
        
        <p>Card image popups are created using the <code>[mtg_card]</code> shortcode. These popups will display when a user hovers their mouse over the name of the card.</p>

        <p>The simplest version of a card link puts the name within tags, like so: <code>[mtg_card]Tarmogoyf[/mtg_card]</code>. That will show the default printing returned by Scryfall (usually the most recent). Different printings can be specified by passing additional parameters to the shortcode.</p>

        <p>The examples below are intended to showcase common use-cases. For a comprehensive explanation of search parameters, refer to the <a href="https://github.com/questionmuppet/mtg-publisher-tools#readme" target="_blank">MTG Publisher Tools documentation</a>.</p>

        <?php $examples = [
            '[mtg_card set="PGPX"]Stoneforge Mystic[/mtg_card]',
            '[mtg_card name="Insectile Aberration"]A blue nacatl[/mtg_card]',
            '[mtg_card set="RAV" number="81" language="JA"]Bob[/mtg_card]',
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
        
        <p>Within Classic blocks and the Classic Editor, <code>[mtg_card]</code> tags can be inserted using the toolbar. To do this, highlight the text you want to wrap and click the card link button.</p>
        
        <img
            src="<?php echo esc_url( MTGTOOLS__ASSETS_URL . 'img/card-link-tags-toolbar.png' ); ?>"
            alt="Toolbar with card link button selected"
            class="mtgtools-screenshot"
        />
        
    </div>

</section>