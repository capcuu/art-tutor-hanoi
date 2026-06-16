# Art Tutor Hanoi — Child Theme

WordPress child theme of **Masu** (`masu-wpcom`) implementing the v2 design system.

## Activate

1. **Appearance → Themes** → activate **Art Tutor Hanoi**
2. **Settings → Permalinks** → Save (flushes rewrite rules for `/courses/{slug}/`, etc.)
3. Theme activation auto-creates pages: About, Courses, Kids Courses, Pricing, Calendar, Book

## Set homepage

**Settings → Reading** → set a static homepage (any page) or leave as latest posts — `front-page.php` renders the v2 homepage.

## Templates (7 entry points)

| File | URL | Content partial |
|------|-----|-----------------|
| `front-page.php` | `/` | `home-content.php` |
| `page.php` | Routed + editor pages | `page-content.php` → slug partial |
| `page-templates/book.php` | `/book/?tab=…` | `book-content.php` |
| `page-templates/students-artworks.php` | `/students-artworks/` | `students-artworks-content.php` |
| `templates/course-detail.php` | `/courses/{slug}/`, `/kids-courses/{slug}/` | `course-content.php` |
| `templates/experience-detail.php` | `/workshops/{slug}/` | `experience-content.php` |
| `templates/single-learner-artwork.php` | Student post | `single-learner-artwork-content.php` |

### Routed via `page.php` (slug → partial)

`about`, `courses`, `kids-courses`, `pricing`, `weekly-calendar`, `thank-you`, `link`

## Dynamic routes

- `/courses/{slug}/` — adult course detail
- `/kids-courses/{slug}/` — kids course detail
- `/workshops/{slug}/` — trial, life drawing, residency, etc.

## Fluent Forms

Book page has two tabs: **Adult** and **Kids**. Create each form in **Fluent Forms → Add New Form**, then wire the IDs in `config/book-tabs.php` or via filters:

```php
add_filter( 'ath_adult_booking_form_id', fn() => 36 ); // your new adult form ID
add_filter( 'ath_kids_booking_form_id', fn() => 21 );
```

The theme embeds forms with `[fluentform id="…"]` on `/book/?tab=adult` and `/book/?tab=kids`.

## Assets

CSS/JS/images copied from `/v2/`. Source of truth for design changes: update `assets/` in this theme.
