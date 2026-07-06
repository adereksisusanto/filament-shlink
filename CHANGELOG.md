# Changelog

All notable changes to `filament-shlink` will be documented in this file.

## v1.0.1 - 2026-07-06

### What's Changed

- docs: add translations section to README.md with available languages
- feat: add multilingual support for short URL management in filament-shlink.php
- chore: clean up CHANGELOG.md by removing initial release section
- chore: update CHANGELOG.md for v1.0.0

## v1.0.0 - 2026-07-06

### What's Changed

- fix: merge changelog update into release.yml (release event not triggered by API)
- fix: pass workflow_dispatch input via env var to prevent template injection
- fix: replace softprops/action-gh-release with gh release create (zizmor superfluous-actions)
- fix: add persist-credentials false to release.yml, suppress zizmor in update-changelog.yml
- fix: move zizmor ignore comment to uses line
- fix: escape release body in update-changelog.yml using jq
- feat: update composer.json and README for Laravel 13 support, add new SVG images
