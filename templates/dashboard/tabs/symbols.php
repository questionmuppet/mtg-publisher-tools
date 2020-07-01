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

<section>
    
    <div class="mtgtools-flex horiz">
        
        <div style="max-width: 600px;" class="mtgtools-flex-item">
            
            <p><a>Check for updates</a></p>
            
            <p>The following mana symbols are available for use in your articles and themes. Simply wrap whatever text you want to parse in [mana_symbols][/mana_symbols] tags. This should work for any text copied directly from Gatherer or Scryfall.</p>
            
        </div>
    
        <div class="mtgtools-flex-item">
        
            <?php $dashboard->display_table( 'symbol_list' ); ?>
        
        </div>

    </div>

</section>