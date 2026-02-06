# Scripts, WP Commands etc.
## Panagea CLI commands
All commands are run via composer WP-CLI from the repo root.

- `composer wp panagea-core apply-defaults`
  - Injects `assets/css/global-overrides.css` into the active theme’s **Additional CSS**.
  - Removes any previous Panagea marker block (`/* Panagea Core Defaults START/END */`) before appending the fresh one.
- `composer wp panagea-core apply-defaults -- --force-blocksy`
  - Same as above, plus overwrites Blocksy theme_mods (palette, buttons, typography) with Panagea defaults.
- `composer wp panagea-core blocksy-defaults`
  - Seeds Blocksy theme_mods only where empty (non-destructive).
- `composer wp panagea-core blocksy-defaults -- --force`
  - Overwrites Blocksy theme_mods with Panagea defaults without touching Additional CSS.
- `composer wp panagea-core clear-additional-css`
  - Removes the Panagea marker-wrapped block from Additional CSS, leaving other custom CSS untouched.

When to use:
- After theme activation/switch.
- After updating Panagea styling tokens.
- To realign a site that drifted from the baseline.