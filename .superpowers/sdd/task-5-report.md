# Task 5 verification report

## Status

Verified after two narrowly scoped corrections discovered during final audit:

- Product copy now also normalizes the unambiguous mixed-language variants `с двох сторін` and `С двох сторін`.
- A follow-up product migration persists that correction for the five approved product fields.
- A category migration corrects the persisted, user-visible `Підїздні` category label and its SEO fields without changing relation or URL fields.

No push was performed. Browser automation was intentionally not used because the browser backend is unavailable; no Playwright or browser dependencies were installed.

## TDD evidence

1. `docker compose exec -T app vendor/bin/phpunit tests/Unit/ProductCopyTest.php`
   - RED: two expected failures for `с двох сторін` and `С двох сторін` (the normalizer returned the original values).
   - GREEN after adding the two reviewed mappings: `OK (12 tests, 13 assertions)`.
2. `docker compose exec -T app vendor/bin/phpunit tests/Feature/ProductCopyMigrationTest.php`
   - RED: required follow-up migration file was missing.
   - GREEN: `OK (2 tests, 10 assertions)`.
3. `docker compose exec -T app vendor/bin/phpunit tests/Feature/CategoryCopyMigrationTest.php`
   - RED: required category migration file was missing (after correcting an invalid foreign-key test fixture).
   - GREEN: `OK (1 test, 5 assertions)`.

## Automated validation

- `docker compose exec -T app vendor/bin/phpunit`
  - `OK (87 tests, 7389 assertions)`.
- `npm test`
  - `3/3` JavaScript tests passed.
- `npm run production`
  - completed successfully.

Warnings observed but not changed because they are outside this correction:

- Docker Compose reports that the top-level `version` attribute is obsolete.
- Browserslist reports that `caniuse-lite` is outdated.

## Database evidence

- The original migration `2026_07_24_090000_normalize_ukrainian_product_copy` was already applied.
- Before correction, the raw published-product audit found five `с двох сторін` occurrences in `products.text`, IDs `27` through `31`; the other 16 original reviewed variants were zero.
- `php artisan migrate --force` applied:
  - `2026_07_24_114800_normalize_remaining_mixed_language_product_copy`
  - `2026_07_24_115100_normalize_category_copy`
- After both migrations, a raw SQL audit returned zero occurrences for all 19 reviewed variants across all and published product fields (`title`, `text`, `description`, `seo_title`, `seo_description`) and category fields (`title`, `seo_title`, `seo_description`).
- During product-page smoke testing, `/product/md002` initially revealed `Підїздні` in the shared navigation. The source was persisted `categories.id = 16`, not a product field; the category migration corrected it. The repeated rendered-page audit was clean.

## HTTP smoke evidence

Against `http://localhost:8081`:

| URL | HTTP | H1 | Verification |
| --- | ---: | ---: | --- |
| `/` | 200 | 1 | All five direct commercial links present |
| `/metalovi-dveri-lutsk` | 200 | 1 | Reachable |
| `/bronovani-dveri-lutsk` | 200 | 1 | Reachable |
| `/vkhidni-dveri-v-budynok-lutsk` | 200 | 1 | Reachable |
| `/vkhidni-dveri-v-kvartyru-lutsk` | 200 | 1 | Reachable |
| `/mizhkimnatni-dveri-z-montazhem-lutsk` | 200 | 1 | Reachable |
| `/product/md002` | 200 | 1 | Correct title/body copy; zero reviewed dirty variants |
| `/product/dveri-vhidni-standart` | 200 | 1 | Correct title/body copy; zero reviewed dirty variants |

## Git review

- Branch: `codex/docker-port-8081`.
- The worktree was clean before verification began.
- `git diff --check` is clean.
- Only correction-related source, migrations, tests, and this report are included. No `.env`, database dump, credentials, generated assets, or unrelated files are staged.
- Existing ignored knowledge-image assets remain ignored and were not modified by this task.
- This report and the correction are committed as `fix: normalize remaining mixed-language product copy`; pushing is intentionally skipped for the whole-branch review.
