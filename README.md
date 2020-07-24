# MTG Publisher Tools
MTG Publisher Tools is a WordPress plugin that facilitates the addition of Magic: The Gathering content to your posts, pages, and themes. This is accomplished using a series of shortcodes, which can be used to generate mana symbols, set symbols, and hover-over popup images.

If you've used plugins like this before, the setup should be familiar. Copy the following into a post or page and check the preview:

    [mana_symbol key="{W}"]
    [mana_symbols]{T}: Add {G}.[/mana_symbols]
    My favorite card is [mtg_card]Silkbind Faerie[/mtg_card].

Hover over the link, and you should see an image of the card pop up. Voilà!

## Mana Symbols
Mana symbols can be shown inline with text using the `[mana_symbols]` and `[mana_symbol]` shortcodes. The first one is used to wrap entire passages, and can be applied to parse Oracle text.

    [mana_symbols]Escape—{G}{G}{U}{U}, Exile five other cards from your graveyard. (You may cast this card from your graveyard for its escape cost.)[/mana_symbols]

The second one shows a single mana symbol. It takes one argument, `key`, corresponding to the symbol to be inserted.

    [mana_symbol key="{15}"]

These codes follow the official convention for plaintext mana symbols established by Magic's [Comprehensive Rules][1]. For reference, a list of all currently available symbols can be found in plugin settings on the "Mana Symbols" tab.

## Card Popups
Card popups make it easier for readers to follow along in articles when they may not know a particular card's text. They take the form of a hyperlink that activates when the user hovers over it with the mouse. Popup links are created using the `[mtg_card]` shortcode.

    [mtg_card]Emrakul, the Aeons Torn[/mtg_card]

That will display the default printing of Emrakul (usually the most recent). To specify a different version, additional parameters can be passed to the shortcode.

### Search Parameters
MTG Publisher Tools will try to narrow down its search using any filters you provide. Note that in cases where multiple cards match, the image displayed may be unpredictable. Each of the following parameter schemes can be used to locate a card image.

* By name, with optional set code – `[mtg_card name="Lightning Bolt" set="M10"]Bolt[/mtg_card]`
* By set + collector number, with optional language – `[mtg_card set="RAV" number="81" language="JA"]Dark Confidant[/mtg_card]`
* By unique id – `[mtg_card id="11bf83bb-c95b-4b4f-9a56-ce7a1816307a"]Delverino[/mtg_card]`

Note that `set` and `id` take their values from Scryfall. `id` is an internal value used to uniquely identify a particular printing. To find this value for a card you will have to query the [Scryfall API][4] directly.

To find valid codes to use in the `set` parameter, consult the [Scryfall set listing][3].

### A Note on Spelling
Be careful to spell your card correctly, or you card link will not render! Punctuation will generally not mess anything up, but a misspelling will. When in doubt, google the card or look it up on Scryfall to ensure the correct spelling.

## Toolbar Buttons
MTG Publisher Tools adds toolbar buttons to the WordPress editor to make inserting shortcodes easier. These buttons are accessible from within a Classic block, or while using the Classic editor.

![Editor toolbar with shortcode buttons](assets/img/card-link-tags-toolbar.png)

To use them, highlight the text you want to wrap, and click the button. You can also Ctrl+Z to undo.

## Caching and Updates
MTG Publisher Tools sources its data from [Scryfall][2]. To assist with performance, some of this data is stored locally in your database. This includes links to the location of mana-symbol and card images (but not the images themselves). When a user views a page with Magic content, their browser will download the image from the CDN or external location.

By default, MTG Publisher Tools checks periodically to see if your local data is out of date. When an update becomes available, site administrators are alerted by a notice at the top of the WordPress dashboard. Both automated checks and admin notices can be disabled on the Settings page.

Card images work slightly differently. A card uri will be cached for a determined period of time, after which it expires. The next time that image is requested, MTG Publisher Tools will re-fetch the data. This ensures your readers see the latest card images available. The default expiration period is one month, which can be changed in Settings.

[1]: http://magic.wizards.com/en/game-info/gameplay/rules-and-formats/rules
[3]: https://scryfall.com/sets
[4]: https://scryfall.com/docs/api