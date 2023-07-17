# MTG Publisher Tools Changelog
This file documents changes to MTG Publisher Tools. This project adheres to [Semantic Versioning](https://semver.org/).

## [1.1.0] - 2023-07-17
### Changed
* Upgraded PHPUnit dev dependency to version 9.x.
* Upgraded required PHP version to 8.2.
### Fixed
* Synced test-case filenames to match PSR-4 convention for class names.
* Fixed several errors & warnings in testcases arising from updated dependencies.

## [1.0.0] - 2020-07-25
This is the first stable release of the plugin.

### Added
* `[oracle_text]` and `[mana_symbol]` shortcodes for rendering mana symbols.
* `[mtg_card]` shortcode for creating card image popups.
* Toolbar buttons in Classic blocks and the Classic Editor.
* Scryfall data source.
* Automated updates and admin notices.
* Settings dashboard with simple user guide.