# mtg-publisher-tools
WordPress plugin that adds various tools for publishing Magic: The Gathering articles

To show mana symbols in your themes and posts, MTG Publisher Tools stores a small amount of information in your database. When new mana symbols are added to the game, your database needs to be updated to make use of them. Updates may also become available if the provided data source changes the uris where its images are located.

By default, MTG Publisher Tools periodically checks the provided data source to see if new data has been released. When an update becomes available, site administrators are alerted by a notice at the top of the WordPress dashboard. This behavior can be changed on the Settings page.