## Components implementations approaches
### Reusable, localized blocks (Synced Pattern)
We ship design-critical sections as **synced patterns** (`post_type: wp_block`) per locale (EN/ES/IT). Each block is exported from the visual editor (Stackable/Blocksy), stored as HTML in `web/app/plugins/panagea-core/assets/reusable-blocks/{block-key}/{block-key}-{locale}.html`, and seeded by the plugin on activation/`init`. Because these are reusable blocks, every instance stays linked to the source: updates to the stored markup propagate to all pages using that block.

Benefits
- Single source of truth: design and copy changes travel to every usage after a plugin update/version bump.
- Fully visual authoring: layouts are built with the block editor + Stackable, no custom JSX/PHP needed for the preview.
- Locale-aware: one block per locale with concise slugs (`panagea-{block}-{en|es|it}`) so multilingual sites pick the right variant.
- Safe rollouts: per-block versioning (`panagea_reusable_block_version_{key}`) so you can bump only the blocks you touched.

See a comparision with other approaches in [Pattern](#the-patterns-approach) and [Dynamic blocks](#the-dynamic-blocks-approach) sections.

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