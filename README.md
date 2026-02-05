# Panagea Design System on Wordpress
Implementation of Panagea designs as a plugin for wordpress

## Development
In order to setup local environment for development:
1. run `composer install` to install dependencies and wp itself
1. rename `env.sample` to `.env` and adjust values in it
1. run `scripts/setup-salt.sh` from the root folder to generate salts values
1. run command to install core wordpress:
    ```
    wp -- core install \
    --url="http://localhost:8080" \
    --title="Panagea WP Design System" \
    --admin_user="admin" \
    --admin_password="admin" \
    --admin_email="you@example.com" \
    --skip-email
    ```
    (use `composer run wp -- core ...` if not installed globally )
1. ensure blocksy theme is active:
    ```
    composer wp theme activate blocksy
    composer wp plugin activate blocksy-companion
    ```
1. ensure stackable plugin is also active: `composer wp plugin activate stackable-ultimate-gutenberg-blocks`
1. ensure panagea plugin is active: `composer wp plugin activate panagea-core`
1. use `php -S 127.0.0.1:8080 -t web` from root folder to start th site

### Requirements on MacOsX
```
brew install php
brew install composer
brew install mysql
```

To start mysql now and restart at login:
`brew services start mysql`

Or, if you don't want/need a background service you can just run:
`/opt/homebrew/opt/mysql/bin/mysqld_safe --datadir\=/opt/homebrew/var/mysql`

Crate a dev user for the project:
```
$> mysql -u root
mysql> create database panaga_wp_ds;
mysql> create user 'localuser'@'localhost' identified by 'localuser';
mysql> grant all on panaga_wp_ds.* to 'localuser'@'localhost';
mysql> exit

## Test new user access
$> mysql -u localuser -p
mysql> SHOW DATABASES;
```

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
1) Verify: `composer wp db query "SELECT ID, post_title, post_name, post_status, post_date FROM wp_posts WHERE post_type='wp_block';"`
1) If a block looks outdated, delete its `wp_block`: 
    ```
    composer wp db query "
        START TRANSACTION;
        -- remove meta for this block
        DELETE pm
        FROM wp_postmeta pm
        JOIN wp_posts p ON pm.post_id = p.ID
        WHERE p.post_type = 'wp_block'
        AND p.post_name = 'panagea-pillars-en';
        -- remove revisions for this block
        DELETE r
        FROM wp_posts r
        JOIN wp_posts p ON r.post_parent = p.ID
        WHERE p.post_type = 'wp_block'
        AND p.post_name = 'panagea-pillars-en'
        AND r.post_type = 'revision';
        -- remove the block itself
        DELETE FROM wp_posts
        WHERE post_type = 'wp_block'
        AND post_name = 'panagea-pillars-en';
        COMMIT;
        "
    ```
    Afterward, rerun your seeder to recreate just that block if needed.

### The Patterns approach
WordPress “Patterns” are saved block compositions that editors can insert into any page. When a pattern from the Panagea Core plugin is inserted, WordPress creates a new, independent copy of that block layout inside the page content. From that moment on the page holds its own detached version: subsequent edits to the original pattern stored in the plugin do **not** propagate to the page instance, and edits on the page do not affect the plugin copy. This behavior is by design to keep published pages stable. If you need updated markup or content from a pattern, re-insert the pattern or manually adjust the existing page content.

This allows content editors to insert a "Hero Pattern" and then change the text or background image for just that one page without breaking every other page on the site.

Create a new pattern
1) Build the layout in the WordPress editor using Stackable/Blocksy (or native blocks) and copy it as HTML from the List View.\n2) Save that HTML into `web/app/plugins/panagea-core/patterns/<pattern-slug>.php` with the standard pattern header (Title, Slug, Categories, Description).\n3) Register it in `includes/patterns.php` by adding a `register_block_pattern` entry pointing to the file you just created (use the `panagea-general` category or another existing category).\n4) Optional: add translations for title/description using `__()` / `_x()` in the registration array.\n5) Deploy the plugin; editors will find the new pattern in the block inserter under its category.

### The Dynamic Blocks approach
Dynamic blocks are rendered at request time by PHP/JS instead of being copied into the page as static HTML. The page only stores the block shortcode/attributes; the actual markup comes from the plugin every time the page loads. Because the source of truth lives in the plugin, any code or template change to the dynamic block propagates everywhere it is used—unlike patterns, which are detached copies once inserted.

Pros vs patterns:
- Single source of truth: design or logic tweaks in the block apply across all pages instantly.
- Better for data-driven content (queries, taxonomies, API data) where live data is expected.
- Leaner database content; fewer large HTML blobs saved per page.

Cons vs patterns:
- Less per-page freedom; editors cannot safely tweak layout without changing the shared block code.
- Site-wide regressions are possible if a block update introduces a breaking change.
- Heavier reliance on plugin availability and caching; failures or cache issues affect every instance.

## ToDo
- [ ] lock down the design so content editors on the final sites can change the text of the patterns but cannot break the layout
