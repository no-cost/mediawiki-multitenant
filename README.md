# MediaWiki (multitenant)

Master MediaWiki installation for no-cost.site tenants. Changes pushed to `main` are synced to the master tenant on the server during deployment.

## Adding extensions or skins

1. Download the extension/skin source into `extensions/` or `skins/`.
2. Ensure the extension has a valid `extension.json` (or `skin.json` for skins).
3. If the extension has composer dependencies, run `composer update --no-dev` from the repo root. The `composer-merge-plugin` automatically picks up all `extensions/*/composer.json` and `skins/*/composer.json` and installs dependencies into the root `vendor/`.
4. Register/load the extensions/skins in `modules.php`.
5. Commit and push to `main`, so it can be deployed from the [no-cost/deploy](https://github.com/no-cost/deploy) repo.
