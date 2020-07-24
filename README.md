# MTG Publisher Tools
MTG Publisher Tools is a WordPress plugin that facilitates the addition of Magic: The Gathering content to your blog. This is accomplished using a series of shortcodes, which insert mana symbols or hover-over card popups into posts, pages, and themes.

If you've used plugins like this before, the setup should be familiar. Copy the following into a post or page and check the preview:

    [mana_symbol key="{W}"]
    [mana_symbols]{T}: Add {G}.[/mana_symbols]
    My favorite card is [mtg_card]Silkbind Faerie[/mtg_card].

Hover over the link, and you should see an image of the card popup. Voilà!

## Mana Symbols

## Card Popups

## Toolbar Buttons
MTG Publisher Tools adds toolbar buttons to the WordPress editor to make inserting shortcodes easier. These buttons are accessible from within a Classic block, or while using the Classic editor.

![Editor toolbar with shortcode buttons](/assets/img/card-link-tags-toolbar.png)

To use them, highlight the text you want to wrap, and click the button.

## Caching and Updates
MTG Publisher Tools sources its data from [Scryfall][1]. To assist with performance, some of this data is stored locally in your database. This includes links to the location of mana-symbol and card images (but not the images themselves). When a user views a page with Magic content, their browser will download the image from the CDN or external location.

By default, MTG Publisher Tools will periodically check Scryfall to see if your local data is out of date. This applies to mana symbols and set symbols. When an update is available, site administrators are alerted by a notice at the top of the WordPress dashboard. Both automated checks and admin notices can be disabled on the Settings page.

Card images work slightly differently. A card uri will be cached for a determined period of time, after which it expires. The next time that image is requested, MTG Publisher Tools will re-fetch the data from Scryfall. This ensures your readers see the latest card images available. By default, image uris expire after one month. This can be changed in Settings.