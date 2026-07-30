# Namugongo Parents' Primary School (NPPS) — Website

A full PHP + MySQL school website: public site, admissions/contact/newsletter
forms, and an admin panel — built on the supplied `school_website_db` schema.

## 1. Setup (XAMPP or similar)

1. **Copy the site** into your web root, e.g. `C:\xampp\htdocs\npps\`.
2. **Import the database, in this exact order**, in phpMyAdmin:
   1. `database/school_website_db.sql` — the schema exactly as supplied (untouched)
   2. `database/db_fixes.sql` — required structural fixes (see §3 below)
   3. `database/seed_data.sql` — NPPS content, 3+ rows in every table
3. **Config** — `config/database.php` already points at `school_website_db`
   with XAMPP defaults (`root` / blank password). Change `DB_USER`/`DB_PASS`
   if your setup differs.
4. Start Apache + MySQL, open `http://localhost/npps/`.

### Admin login

Three accounts are seeded (passwords below are the plain text — the
database only stores a bcrypt hash):

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `admin@npps.ac.ug` | `NppsAdmin@2026` |
| Editor | `editor@npps.ac.ug` | `NppsEditor@2026` |
| Staff | `staff@npps.ac.ug` | `NppsStaff@2026` |

Login at `admin/login.php`. **Change these passwords after first login**
(there's no self-service "change password" screen yet — update via
`admin/create_admin.php`, which makes a new account, then deactivate/delete
the old ones directly in the `admin_users` table). Delete
`admin/create_admin.php` once you no longer need it.

## 2. What this actually is

Two things you gave me got merged:

- **`schoolwebsite26`** — a more complete PHP structure (real form handling,
  CSRF, validation, an admissions workflow) built against this same raw
  database dump, for a similarly-located primary school near Namugongo.
- **`NPPS`** — your own in-progress build, already correctly wired to the
  `school_website_db` schema, with your own design, images and admin panel.

I used your NPPS code and design as the base (it was already the better fit
for this schema) and brought over the missing *structure* from
`schoolwebsite26`: the admissions enquiry workflow, CSRF/spam protection,
a staff directory, FAQs, and a newsletter sign-up — then re-wrote all of it
end-to-end with content specific to Namugongo Parents' Primary School.

## 3. Errors found and fixed

### In the database (`database/db_fixes.sql`)

The raw dump you supplied was originally written for a **secondary**
school template (Senior One–Six) and reused here for a **primary** school,
which left a few things broken or mismatched. Every fix is commented in
the file itself; the headline ones:

- `news` had a `UNIQUE` key on **both** `category_id` and `views`. In
  practice that means only one article could ever exist per category, and
  two articles could never both have 0 views — the second real article
  insert would fail outright. Both indexes are dropped.
- `admission_enquiries.entry_level` was `enum('S1','S2')` — Senior One/Two —
  so the admissions form had no valid value to store for a P1–P7 applicant.
  Widened to Baby Class through P7.
- A long list of `NOT NULL` columns had no `DEFAULT` (`contact_messages.replied_at`,
  `events.start_time`/`end_time`, `admin_users.profile_photo`, `faqs.sort_order`,
  and more) — meaning any insert that didn't explicitly set every one of
  those columns was rejected by MySQL. Each now has a sensible default.
- `contact_messages.is_read` defaulted to **1**, so every new message
  arrived pre-marked "already read" and would never show up as needing
  attention. Now defaults to 0.
- `newsletters_subscribers.email` had no unique constraint (duplicate
  sign-ups possible) and `unsubscribed_at` was `NOT NULL` with no default
  (so a brand-new subscriber, who has never unsubscribed, could not be
  inserted at all). Fixed both.
- `admin_users.created-at` — a hyphen instead of an underscore, a typo in
  the original dump — renamed to `created_at` for consistency.

Nothing in `db_fixes.sql` changes what a table is *for* — it's all missing
defaults, one bad rename, and the two bogus unique keys.

### In the PHP (already fixed in this build)

- **Double-escaping bug**: `clean()` was calling `htmlspecialchars()` on
  input on the way *in*, while every page template *also* escapes on the
  way *out*. An apostrophe in a name would get encoded once on save and
  encoded again on display, turning `O'Brien` into `O&amp;#039;Brien` on
  the page. `clean()` now only trims/strips slashes; escaping happens once,
  at output.
- **Fake "unique views" workaround**: because `news.views` used to have a
  bogus unique index (see above), the admin news form was giving every new
  article a fake, huge "view count" (`microtime()`-based) just to satisfy
  the constraint. Now that the index is gone, new articles simply start at
  0 views and increase for real each time `article.php` is read.
- **No CSRF protection**: an earlier revision added a per-session CSRF token
  to every public and admin form. It has since been removed at your request —
  none of the forms carry or check a CSRF token anymore.
- **No spam honeypot**: public forms include a hidden `website` field —
  invisible to real visitors, but bots fill in every field they find, so a
  submission with that field non-empty is silently dropped.
- **Testimonials had no admin-managed table with nowhere to display them**:
  the admin panel could manage testimonials, but no public page actually
  showed them. They now appear on the homepage and About page.
- **Admissions had no real enquiry form**: the page only said "contact us."
  It now has a form that saves directly into `admission_enquiries`, with a
  matching admin screen (`admin/enquiries.php`) to track and update status.
- **`staff` / `departments` tables were completely unused**: there was no
  staff directory anywhere on the site. Added `staff.php` (public) plus
  leadership/department queries — no admin UI for editing staff yet (see
  §5), but the data is live and displayed.
- **`faqs` table unused**: FAQs now render (by category) on the Contact,
  Admissions and Fees pages.
- **404 status**: `article.php` now actually sends a 404 HTTP status for a
  missing/unpublished article, instead of silently returning 200 with an
  "Article Not Found" message.

## 4. Every table now has real NPPS content

`database/seed_data.sql` seeds **3 or more rows in every single table** —
admin accounts, departments, staff, subjects, news, events, testimonials,
gallery (3 albums, 23 photos), admission requirements & documents, FAQs
(15, across 5 categories), page-content blocks, plus demo admission
enquiries, contact messages, newsletter subscribers, and audit-log entries
so nothing in the schema is left empty.

A couple of tables (`page_content`, `subjects`) are seeded with real data
but intentionally have no admin-editing screen yet — same scope decision
`schoolwebsite26` made for its own extra tables. `about.php` does pull two
of the `page_content` rows (History, Facilities) live from the database,
so it's not entirely inert.

## 5. Known scope limits (not bugs, just not built)

- No admin screen for `staff`/`departments`/`subjects` — edit directly in
  the database for now if you need to change a teacher's bio or add a new
  one.
- No file-upload UI — image paths for news/events/gallery/staff are typed
  in as text (`assets/images/...`); upload the actual file via FTP/file
  manager first.
- The three `admission_documents` (application form, fee structure,
  transfer checklist) are listed as informational text, not clickable
  downloads — the PDFs referenced don't exist yet. Add the real files to
  `assets/documents/` and turn `doc-title` into a link once they do.
- No outbound email — the contact/admissions/newsletter forms save to the
  database only. Wire up `mail()` or an SMTP library in the three
  `process_*.php` files if you want email notifications too.
