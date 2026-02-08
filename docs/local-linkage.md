## Local linkage for panagea-core (Composer, symlinked)

Goal: work on the plugin locally and have changes appear immediately in a consumer WordPress project, similar to `npm link`.

### Prereqs
- The consumer project uses Composer with installer paths for WordPress plugins.
- You have the plugin source checked out locally (either the monorepo or the split branch) so the plugin root exists at a known path.

Example folder layout:
```
/dev/panagea-wp-design-system        # contains web/app/plugins/panagea-core
/dev/consumer-site                   # your WP project using Composer
```

### Composer setup in the consumer project
Add a `path` repository pointing at the local plugin and enable symlinks:
```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../panagea-wp-design-system/web/app/plugins/panagea-core",
      "options": { "symlink": true }
    }
  ],
  "require": {
    "panagea-net/panagea-core": "dev-main",
    "composer/installers": "^2.2"
  },
  "extra": {
    "installer-paths": {
      "wp-content/plugins/{$name}/": ["type:wordpress-plugin"]
    }
  },
  "minimum-stability": "dev",
  "prefer-stable": true
}
```

Install/update with the path preference:
```bash
composer update panagea-net/panagea-core --prefer-install=path
# or: composer install
```

### What happens
- Composer symlinks `wp-content/plugins/panagea-core` to your local plugin folder.
- Edits in the plugin checkout are reflected immediately in the consumer site; no rebuild needed.
- If you add dependencies inside the plugin, run `composer install` in the plugin folder, then rerun `composer update` in the consumer if needed.

### Switching back to published packages
1. Remove or comment out the `path` repository from `composer.json`.
2. Require a tagged version: `composer require panagea-net/panagea-core:^0.1`.
3. Run `composer update panagea-net/panagea-core`.

### Notes
- Use a dev constraint (`dev-main` or the branch you develop on) because path repositories resolve dev refs.
- On Windows, symlinks may need Developer Mode/admin rights.
- Keep the relative path correct for your folder layout. If the consumer project moves, adjust `url`. 
