-- ============================================================
--  db_fixes.sql — Required fixes on top of school_website_db.sql
--  Namugongo Parents' Primary School (NPPS)
--
--  Import order:
--    1. school_website_db.sql   (the structure exactly as supplied — untouched)
--    2. db_fixes.sql            (this file)
--    3. seed_data.sql           (NPPS content)
--
--  Nothing here changes what any table is FOR. Every change below
--  is one of two kinds:
--    (a) a NOT NULL column with no DEFAULT, which makes MySQL/MariaDB
--        reject any INSERT that doesn't explicitly name that column —
--        the moment a form or a future ALTER forgets one field, the
--        whole insert fails.
--    (b) a UNIQUE key or ENUM that only makes sense for the
--        secondary-school (S1–S6) template this dump was copied from,
--        and silently breaks the primary-school (P1–P7) version of
--        the same form.
--  Each block says which symptom it prevents.
-- ============================================================

USE school_website_db;

-- ------------------------------------------------------------
-- admin_users
-- `created-at` has a hyphen instead of an underscore — a typo in
-- the original dump. Nothing else in the schema is named like it;
-- renamed for consistency with created_at/updated_at everywhere else.
-- profile_photo had no DEFAULT, so creating an admin without a
-- photo (the normal case) failed.
-- ------------------------------------------------------------
ALTER TABLE admin_users
  CHANGE `created-at` `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  MODIFY profile_photo VARCHAR(500) NOT NULL DEFAULT '';

-- ------------------------------------------------------------
-- admission_documents — downloads counter had no starting value
-- ------------------------------------------------------------
ALTER TABLE admission_documents
  MODIFY downloads INT(10) UNSIGNED NOT NULL DEFAULT 0;

-- ------------------------------------------------------------
-- admission_enquiries
-- entry_level shipped as enum('S1','S2') — a secondary-school
-- (Senior One/Two) enum reused on a primary (P1–P7) template, so
-- the admissions enquiry form could not record which class a
-- family was applying to. Widened to the actual grades this school
-- teaches. ple_aggregate (Primary Leaving Exam aggregate) only
-- applies to a P7 transfer late in the year, so it must be optional
-- rather than a required field on every enquiry.
-- ------------------------------------------------------------
ALTER TABLE admission_enquiries
  MODIFY entry_level ENUM('Baby Class','Middle Class','Top Class','P1','P2','P3','P4','P5','P6','P7') NOT NULL,
  MODIFY ple_aggregate TINYINT(3) UNSIGNED NULL DEFAULT NULL,
  MODIFY status ENUM('new','contacted','enrolled','declined') NOT NULL DEFAULT 'new',
  MODIFY admin_notes TEXT NOT NULL DEFAULT '';

-- ------------------------------------------------------------
-- audit_log — description/table_name/record_id had no defaults;
-- auditLog() always supplies them, but this keeps direct inserts safe.
-- ------------------------------------------------------------
ALTER TABLE audit_log
  MODIFY description TEXT NOT NULL,
  MODIFY table_name VARCHAR(80) NOT NULL DEFAULT '',
  MODIFY record_id INT(10) UNSIGNED NOT NULL DEFAULT 0;

-- ------------------------------------------------------------
-- contact_messages
-- is_read defaulted to 1, i.e. every new message arrived already
-- marked "read" and would never show up as needing attention.
-- replied_at was NOT NULL with no default, so every insert that
-- didn't set it (i.e. every new message) failed outright.
-- ip_addess keeps its original (misspelled) column name from the
-- supplied dump — only the missing default is fixed here.
-- ------------------------------------------------------------
ALTER TABLE contact_messages
  MODIFY phone VARCHAR(30) NOT NULL DEFAULT '',
  MODIFY subject VARCHAR(300) NOT NULL DEFAULT '',
  MODIFY ip_addess VARCHAR(45) NOT NULL DEFAULT '',
  MODIFY is_read TINYINT(1) NOT NULL DEFAULT 0,
  MODIFY replied_at DATETIME NULL DEFAULT NULL,
  MODIFY created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- ------------------------------------------------------------
-- departments — description/head_of_dept had no defaults
-- ------------------------------------------------------------
ALTER TABLE departments
  MODIFY description TEXT NOT NULL,
  MODIFY head_of_dept VARCHAR(200) NOT NULL DEFAULT '';

-- ------------------------------------------------------------
-- events — location/description/featured_img had no defaults;
-- start_time/end_time were NOT NULL DATETIME with no default,
-- so an event without an explicit time range could never be saved.
-- is_published had no default (every new event silently invisible).
-- ------------------------------------------------------------
ALTER TABLE events
  MODIFY description TEXT NOT NULL,
  MODIFY location VARCHAR(200) NOT NULL DEFAULT '',
  MODIFY featured_img VARCHAR(500) NOT NULL DEFAULT '',
  MODIFY is_published TINYINT(1) NOT NULL DEFAULT 1,
  MODIFY start_time DATETIME NULL DEFAULT NULL,
  MODIFY end_time DATETIME NULL DEFAULT NULL;

-- ------------------------------------------------------------
-- faqs — sort_order/is_published had no defaults
-- ------------------------------------------------------------
ALTER TABLE faqs
  MODIFY sort_order SMALLINT(6) NOT NULL DEFAULT 0,
  MODIFY is_published TINYINT(1) NOT NULL DEFAULT 1;

-- ------------------------------------------------------------
-- gallery_albums / gallery_photos — no defaults on optional fields
-- ------------------------------------------------------------
ALTER TABLE gallery_albums
  MODIFY description TEXT NOT NULL,
  MODIFY cover_image VARCHAR(500) NOT NULL DEFAULT '',
  MODIFY sort_order SMALLINT(6) NOT NULL DEFAULT 0,
  MODIFY is_published TINYINT(4) NOT NULL DEFAULT 1;

ALTER TABLE gallery_photos
  MODIFY caption VARCHAR(300) NOT NULL DEFAULT '',
  MODIFY sort_order SMALLINT(6) NOT NULL DEFAULT 0;

-- ------------------------------------------------------------
-- news
-- The dump put a UNIQUE key on BOTH news.category_id and news.views.
-- A unique category_id means only one article could ever exist per
-- category; a unique views count means two articles could never
-- share the same view total (i.e. never both be 0). As shipped, the
-- second article insert of any kind fails. Both indexes are dropped;
-- views now simply starts at 0 and increments as article.php is read.
-- ------------------------------------------------------------
ALTER TABLE news
  DROP INDEX category_id,
  DROP INDEX `views`,
  MODIFY views INT(10) UNSIGNED NOT NULL DEFAULT 0,
  MODIFY featured_image VARCHAR(500) NOT NULL DEFAULT '',
  MODIFY excerpt TEXT NOT NULL,
  MODIFY body LONGTEXT NOT NULL,
  MODIFY is_featured TINYINT(1) NOT NULL DEFAULT 0,
  MODIFY is_published TINYINT(1) NOT NULL DEFAULT 0;

-- ------------------------------------------------------------
-- news_categories — no defaults on optional fields
-- ------------------------------------------------------------
ALTER TABLE news_categories
  MODIFY slug VARCHAR(120) NOT NULL DEFAULT '',
  MODIFY color VARCHAR(7) NOT NULL DEFAULT '#1565C0';

-- ------------------------------------------------------------
-- newsletters_subscribers
-- Nothing stopped the same address from signing up twice. unsub-
-- scribed_at was NOT NULL DATETIME with no default, so a brand new
-- (never-unsubscribed) subscriber could not be inserted at all.
-- ------------------------------------------------------------
ALTER TABLE newsletters_subscribers
  MODIFY unsubscribed_at DATETIME NULL DEFAULT NULL,
  ADD UNIQUE KEY `email` (`email`);

-- ------------------------------------------------------------
-- page_content — updated_by had no default
-- ------------------------------------------------------------
ALTER TABLE page_content
  MODIFY updated_by INT(10) UNSIGNED NOT NULL DEFAULT 0;

-- ------------------------------------------------------------
-- school_info — description/updated_by had no defaults
-- ------------------------------------------------------------
ALTER TABLE school_info
  MODIFY description VARCHAR(300) NOT NULL DEFAULT '',
  MODIFY updated_by INT(10) UNSIGNED NOT NULL DEFAULT 0;

-- ------------------------------------------------------------
-- staff — is_managemnet (typo preserved from the supplied dump),
-- sort_order and is_active had no defaults
-- ------------------------------------------------------------
ALTER TABLE staff
  MODIFY is_managemnet TINYINT(1) NOT NULL DEFAULT 0,
  MODIFY sort_order SMALLINT(6) NOT NULL DEFAULT 0,
  MODIFY is_active TINYINT(1) NOT NULL DEFAULT 1,
  MODIFY bio TEXT NOT NULL,
  MODIFY email VARCHAR(200) NOT NULL DEFAULT '',
  MODIFY photo VARCHAR(500) NOT NULL DEFAULT '';

-- ------------------------------------------------------------
-- subjects — is_compulsory/sort_order had no defaults
-- ------------------------------------------------------------
ALTER TABLE subjects
  MODIFY is_compulsory TINYINT(4) NOT NULL DEFAULT 1,
  MODIFY sort_order SMALLINT(6) NOT NULL DEFAULT 0,
  MODIFY description TEXT NOT NULL;

-- ------------------------------------------------------------
-- testimonials — no defaults on optional fields
-- ------------------------------------------------------------
ALTER TABLE testimonials
  MODIFY author_role VARCHAR(100) NOT NULL DEFAULT '',
  MODIFY photo VARCHAR(500) NOT NULL DEFAULT '',
  MODIFY rating TINYINT(4) NOT NULL DEFAULT 5,
  MODIFY sort_order SMALLINT(6) NOT NULL DEFAULT 0,
  MODIFY is_published TINYINT(1) NOT NULL DEFAULT 1;
