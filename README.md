# Mbuya Parents' School Website

A full, responsive PHP website for Mbuya Parents' School (Kampala, Uganda),
built in the school's blue & white colours with an original school badge
— plus a built-in **Admin Dashboard** for managing content.

**No database required.** Everything is stored in plain PHP data files, so
this runs on virtually any PHP web host with zero setup — no MySQL, no
phpMyAdmin, nothing to import.

## What's Included

```
mbuya-parents-school/
├── index.php                Home page
├── about.php                 About Us (head teacher's message, values, team)
├── academics.php             Curriculum, PLE track record, events
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
│   ├── admissions.php           View & update inquiry statuses
│   ├── messages.php / message_view.php   View contact messages
│   ├── change_password.php
│   └── includes/                 Shared admin layout, auth guard, uploads
│
├── data/                      ALL SITE CONTENT LIVES HERE (no database)
│   ├── news.php                 News & blog posts
│   ├── gallery.php               Gallery photos
│   ├── events.php                Events / calendar
│   ├── staff.php                  Staff & leadership
│   ├── admissions.php            Submitted admission inquiries
│   ├── messages.php               Submitted contact messages
│   └── admin.php                  Admin login (username + password hash)
│
├── includes/
│   ├── header.php / footer.php   Shared site header & footer
│   ├── config.php                 Site name, address, phone, email
│   └── datastore.php              The file-based "database" layer
│
└── assets/
    ├── css/style.css              Public site styling
    ├── css/admin.css              Admin dashboard styling
    ├── js/script.js               Mobile menu, gallery filter, lightbox
    └── images/                     Badge artwork, gallery/news/staff photos
```

## Getting It Running

1. **Requirements**: PHP 7.4+ — that's it. No database server needed.
2. **Upload the whole folder** to your web host (or place it in a local
   server's document root) and open `index.php` in a browser. The site
   works immediately with the sample content already included.
3. **Make the `/data` folder writable.** This is the only setup step.
   Most hosts default new folders to permissions that already allow this;
   if the admin dashboard ever shows a "could not save" error, set the
   `/data` folder (and its files) to permission `755`/`644` via your
   host's file manager or FTP client.
4. **Log in to the admin dashboard** at `yoursite.com/admin/` with:
   - Username: `admin`
   - Password: `MbuyaAdmin@2026`

   **Change this password immediately** after your first login, from
   Admin Dashboard → Change Password.

## Using the Admin Dashboard

Everything editors need is at `/admin`:

- **News & Blog** — write, edit, publish/unpublish, or delete posts, with
  cover image upload.
- **Gallery** — upload new photos, organise by category, edit captions,
  delete old ones.
- **Events** — add school calendar events shown on the Academics page.
- **Staff** — manage the leadership team shown on the About page.
- **Admissions** — see every admission inquiry submitted through the
  website, and mark each one New / Contacted / Enrolled / Closed.
- **Messages** — see every message submitted through the Contact page,
  and reply directly by email.

Photo uploads accept JPG, PNG, GIF, WEBP or SVG, up to 5MB, and are saved
into `assets/images/`.

## How Content Storage Works (No Database)

Instead of MySQL, this site stores its content in small PHP files inside
`/data` — one per content type. Each file just returns a PHP array, e.g.:

```php
<?php
return [
  ['id' => 1, 'title' => 'Prize Giving Day', ...],
  ['id' => 2, 'title' => 'New Term Begins', ...],
];
```

The admin dashboard reads and rewrites these files for you — you never
need to edit them by hand (though you safely could, if you're comfortable
with PHP arrays).

**Why `.php` files instead of `.json`?** If someone requests a `.json`
file directly in their browser, they'd see its raw contents — which would
expose the admin password hash and parents' contact details. A `.php`
file, requested directly, is instead *executed* by the server and returns
nothing. This keeps everything private with zero extra server
configuration, on any host.

A `.htaccess` file is also included in `/data` as a second layer of
protection for Apache-based hosts.

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
school colours. Replace them with real school photos any time through the
Admin Dashboard (Gallery → Add Photo, News & Blog → cover image, Staff →
photo) — no need to touch any code.

## Backing Up Your Content

Since everything lives in the `/data` folder, backing up your site's
content is as simple as downloading that one folder via FTP. To restore,
just upload it back.

## Source Notes

School information (motto, address, facilities, academic record) was
compiled from the school's public Facebook page, its official website, and
listings on eschoolmanager.net, Uganda's national school directory.
