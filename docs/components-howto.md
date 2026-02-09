## Components implementations
### Reusable, localized blocks (Synced Pattern)
We ship design-critical sections as **synced patterns** (`post_type: wp_block`) per locale (EN/ES/IT). Each block is exported from the visual editor (Stackable/Blocksy), stored as HTML in `web/app/plugins/panagea-core/assets/reusable-blocks/{block-key}/{block-key}-{locale}.html`, and seeded by the plugin on activation/`init`. Because these are reusable blocks, every instance stays linked to the source: updates to the stored markup propagate to all pages using that block.

Benefits
- Single source of truth: design and copy changes travel to every usage after a plugin update/version bump.
- Fully visual authoring: layouts are built with the block editor + Stackable, no custom JSX/PHP needed for the preview.
- Locale-aware: one block per locale with concise slugs (`panagea-{block}-{en|es|it}`) so multilingual sites pick the right variant.
- Safe rollouts: per-block versioning (`panagea_reusable_block_version_{key}`) so you can bump only the blocks you touched.

See a comparision with other approaches in [Pattern](#the-patterns-approach) and [Dynamic blocks](#the-dynamic-blocks-approach) sections.

#### Add a new localized reusable block (example)
1) In WP editor, build the section visually (Stackable/Blocksy). Suggested wprkflow:
    1. create a new page (such as _Pattern Workbench_)
    1. add a container to wrap all the blocks you'll need
    1. inside the container, add all the blocks you need. Feel free to use external Stackable components
    1. whenever you want (you can continue the edit later) save the shell container as: `Create Pattern`. Note: name, category and sync toggle are not important since we'll change them later.
    1. list the patterns you have stored locally, visiting `wp-admin/edit.php?post_type=wp_block`
    1. Here you can keep editing the Pattern. 
1) For each locale, translate the copy, then copy the block markup (Patterns Editor view → Switch to **_Code editor_** → Copy the code shown there).
1) Create files:
   - `assets/reusable-blocks/<block-key>/<block-key>-en.html`
   - `assets/reusable-blocks/<block-key>/<block-key>-es.html`
   - `assets/reusable-blocks/<block-key>/<block-key>-it.html`
1) In `includes/reusable-blocks.php`, add an entry to the `$blocks` map:
   - `key` => your block key
   - `version` => start at `0.0.1` (bump when markup changes)
   - `base_slug` => short slug (e.g., `pillars`)
   - `titles` => per-locale titles shown in the Reusable Blocks list
   - `files` => paths to the locale HTML files created in step 2
1) Deploy/update the plugin. On activation or `init`, the seeder upserts the localized `wp_block` posts. Editors can insert them via “Reusable blocks” using the locale-specific name.

#### Sync a dev site after updates

1) Bump the `version` for the changed block in `includes/reusable-blocks.php` (e.g., `1.0.1` → `1.0.2`).
1) Run the seeder: `composer wp eval 'panagea_seed_reusable_blocks();'`
1) Verify: `./utils/scripts/pattern_utils.sh list`
1) If a block looks outdated, delete its `wp_block` with: `./utils/scripts/pattern_utils.sh reset panagea-pillars-en`
    
    Afterward, rerun your seeder to recreate just that block if needed.