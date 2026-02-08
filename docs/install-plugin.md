## Install panagea-core via Composer (consumer site)
### Prerequisites
- Blocksy theme + Blocksy Companion plugin must be installed and activated.
- Stackable (Ultimate Gutenberg Blocks) plugin must be installed and activated.

If not already present, install and activate via Composer + WP-CLI (from the consumer project root):
```bash
composer require wpackagist-theme/blocksy wpackagist-plugin/blocksy-companion wpackagist-plugin/stackable-ultimate-gutenberg-blocks
composer wp theme activate blocksy
composer wp plugin activate blocksy-companion
composer wp plugin activate stackable-ultimate-gutenberg-blocks
```

### Panagea-core installation
1. In the consumer WordPress project, ensure Composer is set up to install plugins into `wp-content/plugins` (Bedrock defaults work; otherwise add installer paths as shown):
   ```json
   {
     "require": {
       "composer/installers": "^2.2"
     },
     "extra": {
       "installer-paths": {
         "wp-content/plugins/{$name}/": ["type:wordpress-plugin"]
       }
     }
   }
   ```
2. The plugin is published from this monorepo via the split branch produced by `utils/scripts/publish.sh`. Point Composer to that branch on GitHub:
   ```json
   {
     "repositories": [
       { "type": "vcs", "url": "https://github.com/panagea-net/panagea-wp-design-system" }
     ]
   }
   ```
   - The publishing script force-pushes the plugin-only contents to branch `panagea-core` and tags releases (`v0.1.0`, etc.) on the same repo. Consumers resolve versions from those tags.
   - If the repo is private, set auth first: `composer config --global github-oauth.github.com <token>` or export `COMPOSER_AUTH`.
3. Require the plugin:
   ```bash
   composer require panagea-net/panagea-core:^0.1
   ```
   To track the branch tip instead of a tag, use: `composer require panagea-net/panagea-core:dev-panagea-core`
   (Composer prepends `dev-` to branch names when you want the moving branch tip; `dev-panagea-core` maps to branch `panagea-core`.)
4. Activate the plugin in WordPress (WP Admin > Plugins) or via WP-CLI:
   ```bash
   wp plugin activate panagea-core
   ```

Notes:
- Versions follow git tags (`v1.0.0`, `v1.1.0`, ...). Tag releases in this repo so Composer can resolve versions.
- For private GitHub access, configure a token: `composer config --global github-oauth.github.com <token>` or set `COMPOSER_AUTH`.
