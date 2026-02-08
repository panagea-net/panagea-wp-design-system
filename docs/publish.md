## How to publish panagea-core (quick steps)

1) Make sure you are on `main`, clean working tree, at repo root.
2) Pick a new version (greater than last tag), e.g. `0.1.1`.
3) Run the publish helper:
   ```bash
   ./utils/scripts/publish.sh 0.1.1
   ```
   The script will:
   - Split `web/app/plugins/panagea-core` into a commit.
   - Force-push that commit to branch `panagea-core` on origin.
   - Tag `v0.1.1` at the split commit and push the tag.
4) Share the tag/version with consumers (see [install docs](./install-plugin.md)).

If nothing changed since the last publish, the script exits early.

## How the publishing/installation flow works (details)

- **Source of truth:** `main` branch holds the full monorepo. The plugin lives under `web/app/plugins/panagea-core`.
- **Publishing branch:** `utils/scripts/publish.sh` creates a subtree split and force-pushes the plugin-only contents to branch `panagea-core` on the same GitHub repo.
- **Tags as releases:** Every publish should create a semver tag (`v0.x.y`) pointing at the split commit. These tags are what consumers install.
- **Consumers (Composer):** In their `composer.json`, consumers add a VCS repo pointing to `https://github.com/panagea-net/panagea-wp-design-system` and require `panagea-net/panagea-core:^0.x`. Composer reads the `panagea-core` branch (and tags) where the plugin root has its own `composer.json` (`type: wordpress-plugin`), so it installs to `wp-content/plugins/panagea-core` via `composer/installers`.
- **Why force-push:** The `panagea-core` branch is a derived artifact (not hand-edited). Regenerating and force-pushing guarantees it matches the current plugin directory in `main`. Tags preserve historical versions, so rollbacks/installs of older versions remain possible.
- **Prereqs for consumers:** They must have Blocksy + Blocksy Companion and Stackable installed/activated; instructions are in [docs/install-plugin.md](install-plugin.md).
