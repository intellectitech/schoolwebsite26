# Namugongo Model Primary School — Website (Upgraded)

A redesigned, professional school website: dark navy background, sky-blue
headers/navigation, white text, SEO-ready markup, a shared PHP include
system, and a built-in **Nexa AI** school assistant widget.

## What changed from the original

**Design**
- New color theme applied site-wide: dark navy (`#071427`) background,
  sky-blue (`#38bdf8` / `#0ea5e9`) navigation, page headers and buttons,
  white text throughout.
- Rebuilt `styles.css` with a proper design-token system (`:root` CSS
  variables), responsive layout, card shadows, hover states, and a real
  footer.
- Sidebar navigation is more polished: rounded active/hover states,
  contact details pinned to the bottom, mobile hamburger toggle.

**Code structure (PHP)**
- Added `includes/config.php` — a single place for the database
  connection (PDO, prepared statements only) and site constants
  (name, phone, email, address, meta description). Previously every
  page hard-coded its own DB credentials.
- Added `includes/header.php` and `includes/footer.php` — shared page
  chrome (nav, SEO `<head>`, footer, Nexa AI widget) instead of
  duplicating the same 40 lines of HTML on every page.
- Converted every page to `.php` (`index.php`, `about.php`,
  `admission.php`, `news.php`, `gallery.php`, `contact.php`) so they all
  share the same header/footer and are easy to extend later.
- `news.php` now pulls posts from the `news_posts` table instead of
  showing static hard-coded text (falls back to sample content if the
  table is empty or the DB is unreachable).
- `dashboard.php` now also shows **Admission Applications** count and a
  simple CSS bar-chart overview, in addition to messages/news/events.
- All form handlers (`submit_admission.php`, `submit_contact.php`,
  `admin_login.php`) validate email format, use prepared statements, and
  no longer leak raw database error text to visitors.
- Login now regenerates the session ID on success (session fixation
  protection).

**SEO**
- Every page has a unique `<title>`, meta description, canonical URL,
  Open Graph + Twitter Card tags, and School structured data (JSON-LD)
  generated from `includes/header.php`.
- Added `robots.txt` and `sitemap.xml`.

**Nexa AI — School Assistant**
- A floating chat widget (bottom-right, sky-blue, on every page) that
  instantly answers common questions about admissions, fees, contact
  details, news, and gallery using a built-in knowledge base — no
  server round-trip or API key needed.
- Code lives in `assets/js/nexa-ai.js`. To connect it to a real AI
  model instead of the built-in knowledge base, replace the body of
  `nexaGetAnswer()` with a `fetch()` call to your own backend endpoint,
  which then calls your AI provider **server-side** (never put an AI
  API key in front-end JavaScript).

## A note on "Laravel"

This project is built as clean, framework-free PHP (PDO + prepared
statements), matching what was already in your two reference projects.
Rewriting the whole site on the Laravel framework is a much larger,
separate job — it means a new project skeleton (`composer`, Artisan,
routing, Eloquent models/migrations, Blade templates) and isn't
something that can be safely bolted onto the existing plain-PHP files.
I kept the code framework-free so it runs anywhere with PHP + MySQL,
and organized it (config/header/footer includes, prepared statements
everywhere) so it would be straightforward to port into Laravel later
if you want — just ask and I can scaffold that as its own project.

## Setup

1. Create the database: import `db_school.sql` into MySQL
   (`mysql -u root -p < db_school.sql`, or via phpMyAdmin).
2. Update credentials if needed in `includes/config.php`
   (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
3. Serve the folder with PHP (`php -S localhost:8000`) or place it in
   your Apache/Nginx web root.
4. Default admin login: username `admin` (password is the one already
   hashed in `db_school.sql` from the original project — change it in
   the `admin_users` table for production use).
5. Update `SITE_URL` in `includes/config.php` and the URLs in
   `sitemap.xml` once you have a real domain.

## File map

```
includes/config.php      database connection + site constants
includes/header.php       shared <head>, SEO tags, sidebar nav
includes/footer.php       shared footer + Nexa AI widget mount
assets/js/nexa-ai.js      Nexa AI assistant widget
styles.css                 full site theme (dark navy / sky blue)
script.js                  scroll reveals, counters, contact form AJAX
index.php / about.php / admission.php / news.php / gallery.php / contact.php
dashboard.php / admin_login.php / logout.php   admin area
submit_admission.php / submit_contact.php       form handlers
db_school.sql              database schema + seed data
robots.txt / sitemap.xml   SEO
```
