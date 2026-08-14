# Architecture

Job Scam Checker is split into a portable WordPress plugin and a custom theme.

The plugin owns application behavior, installation, checker markup, and—beginning
in Phase 2—the rule engine. The theme owns layout, navigation, typography,
metadata, and editorial templates. Production has no build service or package
manager requirement.

Phase 1 intentionally sends no checker request and stores no visitor message.
The browser script handles only the character counter and the development-phase
notice. Phase 2 will introduce a same-origin WordPress endpoint and local PHP rule
analysis.

## Compatibility target

- WordPress 6.4 or newer
- PHP 7.4 or newer
- MySQL/MariaDB supplied by WordPress hosting
- Apache or LiteSpeed with normal WordPress permalinks

## Content installation

Plugin activation calls an idempotent page installer. It creates only missing
pages, never overwrites an existing matching slug, stores the resulting IDs, and
assigns the generated Home page as the static front page. Later phases can append
page definitions to this framework.

## Data in Phase 1

Only the plugin version and generated page IDs are stored in WordPress options.
No pasted messages, analytics, statistics, cookies, or personally identifying
visitor data are stored by the custom code.
