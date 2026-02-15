# MediaWiki (multitenant)

Master MediaWiki installation for no-cost.site tenants. Changes pushed to `main` are synced to the master tenant on the server during deployment.

## Adding extensions or skins

1. Download the extension/skin source into `extensions/` or `skins/`.
2. Ensure the extension has a valid `extension.json` (or `skin.json` for skins).
3. If the extension has composer dependencies, either:
   - include the `vendor/` directory in the download, or
   - add/update the extension's `composer.local.json` and run `composer update` to install them.
4. Register the extension in `modules.php`.
5. Run `scripts/track-vendor.sh` to force-track any `vendor/` directories that are excluded by extension `.gitignore` files (this should also run automatically as a pre-commit hook).
6. Commit and push to `main`, so it can be deployed from the [no-cost/deploy](https://github.com/no-cost/deploy) repo.

## Setup after cloning

Point git to the versioned hooks directory:

```bash
git config --local core.hooksPath .githooks
```
