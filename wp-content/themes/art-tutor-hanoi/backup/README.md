# ATH Content Backup & Rollback

Snapshot created before the Gutenberg migration refactor.

## What was saved

| Location | Contents |
|----------|----------|
| `pre-gutenberg-snapshot/data/` | All PHP data arrays (courses, pricing, teachers, …) |
| `pre-gutenberg-snapshot/partials/` | All `*-content.php` partials + `home-content.php`, `pathways-list.php` |
| `pre-gutenberg-snapshot/inc/` | `generic-page.php` |
| `pre-gutenberg-snapshot/front-page.php` | Homepage template |
| `db-content-backup.json` | Created when you run migration (WP page editor contents) |

## Rollback options (easiest first)

### 1. Legacy mode only (instant, no file restore)

WP Admin → **Tools → ATH Content** → set mode to **legacy** → Save.

Site renders exactly as before using PHP partials. Gutenberg content stays in the database.

### 2. Restore database editor content

Tools → ATH Content → **Restore DB content backup**.

Restores `post_content` from the backup taken at migration time.

### 3. Restore PHP files from snapshot

Copy files from `pre-gutenberg-snapshot/` back into the theme:

```text
pre-gutenberg-snapshot/data/*          → data/
pre-gutenberg-snapshot/partials/*    → partials/
pre-gutenberg-snapshot/inc/*         → inc/
pre-gutenberg-snapshot/front-page.php → front-page.php
```

Also remove or revert `inc/content-mode.php`, `inc/gutenberg-migration.php`, and the updated `partials/page-content.php` if you want the pre-refactor code exactly.

### 4. wp-config override

```php
define( 'ATH_CONTENT_MODE', 'legacy' );
```

Forces legacy rendering regardless of the admin setting.

## After migration

- **Hybrid mode** (default): migrated pages use Gutenberg; dynamic pages stay on PHP.
- Edit migrated pages in **Pages** → block editor (content is in a Custom HTML block — you can split into normal blocks over time).
- Course detail URLs (`/courses/...`, `/kids-courses/...`) and experiences remain in `data/*.php`.
- Homepage gallery always renders via PHP (`partials/home-gallery.php` / `[ath_home_gallery]`) — shuffle and load-more behaviour unchanged.
