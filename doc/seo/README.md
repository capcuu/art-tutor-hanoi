# SEO — Art Tutor Hanoi

## Playbook (làm từng bước)

**Trang hướng dẫn chi tiết trên site:** `/seo/` (noindex, sync từ theme) — có **báo cáo GSC baseline**, **kỳ vọng clicks**, **lịch kiểm tra** (14 / 28 / 56 / 90 ngày).

Sau deploy theme: login WP Admin → trang tự sync, hoặc **Tools → ATH Content Migration → Update SEO playbook**.

## Tiến độ sprint

| Sprint | Nội dung | Trạng thái |
|--------|----------|------------|
| **1** | Rank Math, sitemap, seed meta, Local SEO, redirect 301, GSC | **Xong** |
| **2** | On-page commercial (home intro, workshops, courses pillar, detail meta, cross-links) | **Xong** |
| **3** | Blog 301 duplicates, FAQ + FAQPage schema, archive noindex, blog cross-links | **Xong** |
| **4** | GSC watchlist, A/B title variants, defer JS, 2-week routine | **Deploy + Apply Sprint 4** |

## Dữ liệu GSC (export)

| File | Nội dung |
|------|----------|
| `Queries.csv` | Top queries — clicks, impressions, CTR, position |
| `Pages.csv` | Top landing pages |
| `Chart.csv` | Clicks/impressions theo ngày |
| `Devices.csv` | Desktop vs mobile (desktop CTR rất thấp) |
| `Countries.csv` | Geo |
| `Search appearance.csv` | Product snippets, translated |

## Code theme liên quan

| File | Việc |
|------|------|
| `inc/seo.php` | Rank Math robots, LocalBusiness JSON-LD, seed meta |
| `inc/seo-page.php` | Nội dung `/seo/` |
| `inc/legacy-redirects.php` | 301 slug cũ → URL mới |
| `inc/seo-content.php` | Blog redirects, FAQ schema, archive noindex |
| `data/seo-blog-redirects.php` | Danh sách blog slug → commercial URL |
| `data/seo-gsc-baseline.php` | Baseline impressions/CTR cho monitor |
| `inc/seo-monitor.php` | Defer JS, title A/B variants, Sprint 4 |
| `inc/gutenberg-migration.php` | Sprint 2–4 migration buttons |

## IA / redirect dài hạn

Kế hoạch URL mới (`/classes/…`, `/portfolio/…`): xem `SITE-IA.md` và `sitemap.csv`. Redirect trong theme hiện map **slug cũ → URL hiện tại** (courses, workshops, book…).
