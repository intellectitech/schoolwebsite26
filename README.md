# Uganda Martyrs Primary School — Internship Project (Reassembled)

This is the `internship` project with a working PHP backend: real form
validation, real data storage, and real data retrieval wired up against
the supplied database, plus a full admin panel including news article
management. Everything below was tested end-to-end against a fresh
import of `school_website_db__1_.sql` + `database_patch.sql`.

## 1. Setup (XAMPP / any Apache+PHP+MySQL stack)

1. Copy this whole folder into your server root (e.g. `htdocs/school-website`).
2. Create a database called `school_website_db` (matches `database.php`).
3. Import, **in this exact order**:
   1. `school_website_db__1_.sql` (the original dump you supplied)
   2. `database_patch.sql` (fixes described below — **required**, several
      forms will not work without it)
4. Open the site. `database.php` is already set to XAMPP's defaults
   (`root` / no password) — edit it if your MySQL user is different.
5. Make sure `images/news/` is writable by the web server — that's where
   uploaded article photos are stored.

## 2. Set your own admin password

The three seeded `admin_users` rows all share the same bcrypt hash, and
there's no way to recover the original plain-text password from a hash.
Run this once to set a known password (`ChangeMe123!` — please change it
after logging in once, via a fresh call to PHP's `password_hash()`):

```sql
UPDATE admin_users
SET password = '$2y$10$sRqNtcc/wbOSG/VMHwqaT.ePHF0bBoE9NtRXa.SECgwvUI/uWhgaa'
WHERE email = 'admin@school.com';
```

Log in at `/admin/login.php` with `admin@school.com` / `ChangeMe123!`.

## 3. Managing news from the admin panel

You no longer need to touch the database to publish a story. Log in and
go to **News & Stories** in the sidebar:

- **Add / Edit** — title, category, a short summary (auto-filled from the
  story if you leave it blank), the full story text, an optional photo
  (JPG/PNG/WEBP, 3MB max — validated by actually reading the image data,
  not just trusting the file extension), whether it's Featured, and a
  Draft/Published status with a publish date.
- **List** — tabs for All / Published / Drafts, with Edit, Publish ⇄
  Unpublish, and Delete on every row. Deleting an article also removes its
  uploaded photo from disk. Unpublishing is instant and reversible; delete
  is permanent and asks for confirmation first.
- **Categories** — a small panel on the same page lets you add a new
  category (name + colour) without going near the database. There's no
  delete for categories, since articles reference them by ID.

Every add/edit/delete/publish action is written to `audit_log`, the same
as the rest of the admin panel, and the sidebar shows a badge with your
current draft count.

**On real photos:** this is the one place on the site that departs from
the "illustration only" design system, by your choice — a news article
with an uploaded photo shows that photo (on the homepage, the news
archive, and the article page); one without a photo still falls back to
the hand-drawn hill illustration, so nothing looks broken either way. The
three seeded sample articles have no real photo behind them (their
`featured_image` values point at files that were never supplied), so
they'll show the illustration until you re-save them with a real one.

## 4. Why `database_patch.sql` exists

The supplied dump was written against a **secondary-school** template
(S1–S6, O/A-Level) and reused for a **primary** school (P1–P7). The patch
fixes the places that mismatch, plus a few unrelated bugs found while
wiring up the forms:

| # | Fix | Why |
|---|-----|-----|
| 1 | Creates `vw_staff_directory` view | `about.php`/`staff.php` already queried this view for the leadership section — it just wasn't in the dump |
| 2 | Widens `admission_enquiries.entry_level` to P1–P7 | Was a secondary-school enum (`S1`,`S2`); the admissions form on this site collects primary grades |
| 3 | Makes `ple_aggregate` nullable | Only relevant for older transfers, optional on the form |
| 4 | Makes `contact_messages.replied_at` nullable | Was `NOT NULL` with no default — broke every insert |
| 5 | `contact_messages.is_read` now defaults to `0` | Was defaulting new messages to "already read" |
| 6 | Renames `ip_addess` → `ip_address` | Typo in the original column name |
| 7 | Unique key on `newsletters_subscribers.email` | Nothing stopped duplicate sign-ups |
| 8 | `newsletters_subscribers.unsubscribed_at` nullable | Same `NOT NULL`-no-default problem as #4 |
| 9 | Drops bogus `UNIQUE` indexes on `news.category_id` and `news.views` | As shipped, only **one article per category ever** and no two articles could share a view count — verified this breaks a second insert on the raw dump, and that the patch fixes it |
| 10 | Seeds a few extra `school_info` rows | `school_address`, `hero_title`, `hero_subtitle`, `founded_year`, `total_students` — read by the pages but missing from the dump |

## 5. What was fixed in the PHP

- **`process_contact.php`** — was `require`-ing `config/database.php` and
  `includes/functions.php`, folders that don't exist in this project's flat
  layout, so the contact form was 100% broken (fatal error on every
  submit). Rewritten with the right paths, added a `phone` field (the
  table required it), CSRF protection, a honeypot field, and proper
  redirect-with-flash-message handling.
- **`process_admissions.php`, `process_newsletter.php`** — didn't exist.
  The enquiry form had no `action`/`method` and the newsletter form had no
  backend at all; `script.js` was faking a "submitted!" message on both
  without saving anything. Both now validate and store to the database.
- **`script.js`** — the contact, enquiry and newsletter forms all called
  `preventDefault()` and just displayed a canned "thank you" note, so
  nothing was ever sent to the server. That's removed; forms now really
  submit, with a lightweight "Sending…" button state layered on top.
- **`header.php` / `footer.php`** — pulled settings keys
  (`school_phone`, `school_email`) that don't exist in `school_info`
  (the real keys are `contact_phone`/`contact_email`); the logo `<img>`
  used a Windows-style backslash path that 404s on Linux/Apache; the
  mobile nav had a duplicated "HOME" link.
- **`index.php`** — fetched `$latestNews` and `$events` but never
  displayed them anywhere; added a real "News & upcoming events" section.
- **`news.php`** — fetched a correctly paginated, category-filtered
  `$articles` query, then ignored it and rendered a different, unfiltered,
  unpaginated query instead, with markup classes that don't exist in the
  CSS (`news-card-img` used as the card wrapper, no `.card-footer` style).
  Rebuilt to actually use the paginated query, with working category tabs
  and page numbers.
- **`article.php`** — didn't exist; every "Read more" link on the site was
  dead. Added, with view counting and related articles.
- **`about.php` / `staff.php`** — the leadership query was commented out
  (depended on the missing view). Wired up via `vw_staff_directory`,
  alongside the existing illustrative role cards (kept, and labelled, since
  the school hasn't supplied real leadership names/photos yet).
- **`admin/`** — `login.php` existed but was an empty file; there was no
  dashboard, no way to actually read the contact messages or admissions
  enquiries that the forms save, and no way to write a news article without
  going into the database directly. Built `login.php`, `logout.php`,
  `dashboard.php`, `messages.php`, `enquiries.php`, `news.php`,
  `news-form.php`, `sidebar.php` — session-based auth, CSRF-protected
  actions, audit logging on every change, all styled from the existing
  design tokens (`style.css` appended, nothing inline).
- **`functions.php`** — `clean()` was calling `htmlspecialchars()` on the
  way into the database, while every template *also* calls
  `htmlspecialchars()` on the way out — so an apostrophe in a name (e.g.
  "O'Brien") was stored as `O&#039;Brien` and displayed as the literal
  text `O&amp;#039;Brien`. Storage safety already comes from prepared
  statements everywhere; `clean()` now only trims/normalizes input, and
  display escaping happens once, at output. Also added flash-message,
  old-input-repopulation, CSRF, slug-generation, and image-upload helpers
  used across the site and the admin panel.

## 6. Deliberate scope decisions (not bugs)

- **`gallery.php`** stays illustrated-SVG-only, per the project's design
  system ("no real photography"). The `gallery_albums`/`gallery_photos`
  tables reference `.jpg` files that were never actually supplied, so
  wiring them in would only produce broken image icons — the opposite of
  the intended look. News articles are the one deliberate exception to
  this rule (see §3) — that was a specific choice for news, not a change
  to the site-wide design system.
- **Per-grade class teacher cards** on `staff.php` stay as labelled
  placeholder content — there's no table in the schema for "which teacher
  is assigned to which of P1–P7," so this can't be driven from real data
  without extending the schema further than "implement the existing
  design" called for.
- **Sample `news` and `testimonials` rows** are demo content left over
  from the secondary-school template (e.g. "Best O-Level Results in
  District"). They're realistic enough to prove the pages work, but you'll
  want to replace or edit them via **News & Stories** in the admin panel
  with real primary-school stories and photos.

## 7. Verified end-to-end

Tested against a live MariaDB + PHP instance, not just read through:

- Every public page renders without errors.
- The contact form, admissions form, and newsletter sign-up all validate,
  protect against CSRF and bot spam (honeypot), and write correctly-escaped
  data to the database; duplicate newsletter emails are rejected gracefully.
- Admin login enforces the real password hash and blocks unauthenticated
  access to every admin page.
- Message/enquiry actions (mark read, change status) update the database
  and write to `audit_log`.
- Full news article lifecycle: creating an article with a real uploaded
  photo makes that photo appear correctly on the homepage, news archive,
  and article page; editing an article (including replacing its photo)
  cleans up the old file from disk and keeps the URL slug stable unless
  deliberately changed; unpublishing an article makes its article page
  return 404 immediately, and publishing brings it back; deleting an
  article removes both the database row and its photo file; adding a
  category from the inline form works without touching the database.
