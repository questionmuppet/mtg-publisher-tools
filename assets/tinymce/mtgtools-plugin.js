(function() {
    tinymce.create('tinymce.plugins.mtg_publisher_tools', {
        init : function(editor, url) {
            
			// Mana symbol tags
			editor.addButton('add_mtg_symbols', {
                title : 'Insert Mana Symbol Tags',
                image : url + '/icons/c.svg',
                onclick : function() {
                    var selection = editor.selection.getContent();
                    if (selection != '')
                    {
                        editor.undoManager.beforeChange();
                        editor.selection.setContent('[oracle_text]' + selection + '[/oracle_text]');
                        editor.undoManager.add();
					}
                }
            });

            // Card link tags
			editor.addButton('add_mtg_card_link', {
                title : 'Insert MTG Card Link',
                image : url + '/icons/circle-gold.jpg',
                onclick : function() {
                    var selection = editor.selection.getContent();
                    if (selection != '')
                    {
                        editor.undoManager.beforeChange();
                        editor.selection.setContent('[mtg_card]' + selection + '[/mtg_card]');
                        editor.undoManager.add();
					}
                }
            });
            
        },
		
        createControl : function(n, cm) {
            return null;
        }
		
    });
    tinymce.PluginManager.add('mtg_publisher_tools', tinymce.plugins.mtg_publisher_tools);
})();