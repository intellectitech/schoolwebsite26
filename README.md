# Mbuya Parents' School Website

A full, responsive PHP website for Mbuya Parents' School (Kampala, Uganda),
built in the school's blue & white colours with an original school badge
— plus a built-in **Admin Dashboard**, backed by a **MySQL database**.

## What's Included

```
mbuya-parents-school/
├── index.php                Home page (with upcoming events preview)
├── about.php                 About Us (head teacher's message, values, team)
├── academics.php             Curriculum, PLE track record, events preview
├── events.php                 Full events calendar (upcoming + past events)
├── admissions.php            Admissions info + working inquiry form
├── gallery.php               Photo gallery with category filter + lightbox
├── news.php                  News & Blog listing + single article view
├── contact.php                Contact info, map, working contact form
│
├── admin/                    ADMIN DASHBOARD (password protected)
│   ├── login.php               Sign-in page
│   ├── index.php                Dashboard home (stats + recent activity)
│   ├── news.php / news_edit.php / news_delete.php
│   ├── gallery.php / gallery_edit.php / gallery_delete.php
│   ├── events.php / events_edit.php / events_delete.php
│   ├── staff.php / staff_edit.php / staff_delete.php
│   ├── admissions.php           View, update status, or delete inquiries
│   ├── admissions_delete.php
│   ├── messages.php / message_view.php / message_delete.php
│   ├── admins.php               Manage Admins — create/remove admin logins
│   ├── change_password.php
│   └── includes/                 Shared admin layout, auth guard, uploads
│
├── database/
│   └── mbuya_parents_school.sql   Full schema + starter data — import this
│
├── includes/
│   ├── header.php / footer.php   Shared site header & footer
│   ├── config.php                 Site name, address, phone, email
│   └── db.php                     MySQL connection (PDO) — edit credentials here
│
└── assets/
    ├── css/style.css              Public site styling
    ├── css/admin.css              Admin dashboard styling
    ├── js/script.js               Mobile menu, gallery filter, lightbox
    └── images/                     Badge artwork, gallery/news/staff photos
```

## Getting It Running

1. **Requirements**: PHP 7.4+ with the `pdo_mysql` extension, and a MySQL
   or MariaDB database (standard on virtually all school/shared hosting,
   e.g. cPanel).
2. **Create a database** through your host's control panel (or locally
   via phpMyAdmin/Adminer), then **import the schema**:
   `database/mbuya_parents_school.sql`. This creates every table and
   loads in the starter content (sample news posts, gallery photos,
   staff, events, and the default admin login) in one go.
3. **Set your database credentials** in `includes/db.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'mbuya_parents_school');
   define('DB_USER', 'your_db_username');
   define('DB_PASS', 'your_db_password');
   ```
   Your host's control panel will show you these — they are almost
   never "root" with no password like the local testing defaults.
4. **Update school details**: Open `includes/config.php` to edit the
   phone numbers, email, address and Facebook link shown across the
   site.
5. Upload the whole folder to your web host and open `index.php` in a
   browser.

## Logging Into the Admin Dashboard

Go to `yoursite.com/admin/` and log in with:
- Username: `admin`
- Password: `MbuyaAdmin@2026`

**Change this password immediately** after your first login — Admin
Dashboard → Change Password.

## Using the Admin Dashboard

- **News & Blog** — write, edit, publish/unpublish, or delete posts,
  with cover image upload.
- **Gallery** — upload new photos, organise by category, edit captions,
  delete old ones.
- **Events** — add school calendar events. These show up automatically
  on the new public **Events** page, the Academics page preview, and
  the homepage "Upcoming Events" section.
- **Staff** — manage the leadership team shown on the About page.
- **Admissions** — see every admission inquiry submitted through the
  website. Update each one's status (New → Contacted → Enrolled →
  Closed), and **delete** any inquiry once it's been actioned — for
  example, once a child has been enrolled and you no longer need to
  keep the inquiry record.
- **Messages** — see every message submitted through the Contact page,
  mark read/unread, reply directly by email, or delete it.
- **Manage Admins** — create additional admin logins for other staff
  who help run the website (e.g. a deputy head or bursar), and remove
  accounts that are no longer needed. You can't delete your own account
  while logged in as it, and the system won't let the last remaining
  admin account be deleted, so you can never get permanently locked
  out.

Photo uploads accept JPG, PNG, GIF, WEBP or SVG, up to 5MB, and are
saved into `assets/images/`.

## About the School Badge

The site uses an original badge design (shield, rising sun and open book
in navy blue and gold) created for this project, since the school's
official logo could not be reproduced here. Swap in the real school badge
at any time by replacing `assets/images/badge.svg` and
`assets/images/badge-white.svg` with the official artwork (keep the same
filenames, or update the paths in `includes/header.php`,
`includes/footer.php`, and `admin/includes/admin_header.php`).

## Photos

The gallery, news and staff images are placeholder illustrations in the
school colours. Replace them with real school photos any time through
the Admin Dashboard (Gallery → Add Photo, News & Blog → cover image,
Staff → photo) — no need to touch any code or the database directly.

## Backing Up Your Content

Since everything lives in the MySQL database, back up your site's content
by exporting the database (most hosts offer a one-click "Export" or
"Backup" button in phpMyAdmin, or you can use `mysqldump`). To restore,
import that backup file the same way you imported the original schema.

## Source Notes

School information (motto, address, facilities, academic record) was
compiled from the school's public Facebook page, its official website,
and listings on eschoolmanager.net, Uganda's national school directory.
