# Art Tutor Hanoi — Child Theme

WordPress child theme of **Masu** (`masu-wpcom`) implementing the v2 design system.

## Activate

1. **Appearance → Themes** → activate **Art Tutor Hanoi**
2. **Settings → Permalinks** → Save (flushes rewrite rules for `/courses/{slug}/`, etc.)
3. Theme activation auto-creates pages: About, Courses, Kids Courses, Pricing, Calendar, Book

## Set homepage

**Settings → Reading** → set a static homepage (any page) or leave as latest posts — `front-page.php` renders the v2 homepage.

## Page templates

| Template | URL slug | Notes |
|----------|----------|-------|
| (front page) | `/` | Homepage |
| About | `/about/` | |
| Courses | `/courses/` | |
| Kids Courses | `/kids-courses/` | |
| Programs & Pricing | `/pricing/` | |
| Weekly Calendar | `/calendar/` | TablePress + `[art_calendar]` |
| Book a Class | `/book/` | Fluent Forms ID 13 |

## Dynamic routes

- `/courses/{slug}/` — adult course detail
- `/kids-courses/{slug}/` — kids course detail
- `/workshops/{slug}/` — trial, life drawing, residency, etc.

## Fluent Forms

Default booking form ID: **13**. Override:

```php
add_filter( 'ath_booking_form_id', fn() => 13 );
```

## Assets

CSS/JS/images copied from `/v2/`. Source of truth for design changes: update `assets/` in this theme.
