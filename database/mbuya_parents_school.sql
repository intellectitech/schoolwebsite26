-- ============================================================
-- Mbuya Parents' School Website Database
-- File: mbuya_parents_school.sql
-- Description: Full database schema + starter sample data for
--              the Mbuya Parents' School PHP website and its
--              Admin Dashboard.
-- Import this file in phpMyAdmin, Adminer, or via:
--   mysql -u root -p mbuya_parents_school < mbuya_parents_school.sql
-- ============================================================

-- Force this session to read the special characters below (like the
-- em dash in "Parents' Evening") correctly, regardless of the
-- importing tool's own default charset settings.
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS mbuya_parents_school
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mbuya_parents_school;

-- ------------------------------------------------------------
-- Table: gallery_images
-- (category is a simple free-text field, editable from the
-- admin dashboard — no separate category table needed)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS gallery_images (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  title         VARCHAR(150) NOT NULL,
  category      VARCHAR(100),
  file_path     VARCHAR(255) NOT NULL,
  caption       VARCHAR(255),
  uploaded_at   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: staff (used for "Who We Are" / Team)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS staff (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  full_name     VARCHAR(150) NOT NULL,
  role_title    VARCHAR(150) NOT NULL,
  bio           TEXT,
  photo_path    VARCHAR(255),
  display_order INT DEFAULT 0
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: news_posts (News & Blog)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS news_posts (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  title         VARCHAR(200) NOT NULL,
  slug          VARCHAR(220) NOT NULL UNIQUE,
  category      VARCHAR(100),
  excerpt       VARCHAR(300),
  body          TEXT NOT NULL,
  cover_image   VARCHAR(255),
  is_published  TINYINT(1) DEFAULT 1,
  published_at  DATE DEFAULT (CURRENT_DATE)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: admissions_inquiries (from the Admissions form)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admissions_inquiries (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  parent_name     VARCHAR(150) NOT NULL,
  parent_email    VARCHAR(150) NOT NULL,
  parent_phone    VARCHAR(50) NOT NULL,
  child_name      VARCHAR(150) NOT NULL,
  desired_class   VARCHAR(50) NOT NULL,
  message         TEXT,
  submitted_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  status          ENUM('new','contacted','enrolled','closed') DEFAULT 'new'
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: contact_messages (from the Contact Us form)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS contact_messages (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  full_name     VARCHAR(150) NOT NULL,
  email         VARCHAR(150) NOT NULL,
  subject       VARCHAR(200),
  message       TEXT NOT NULL,
  submitted_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  is_read       TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: events (school calendar / upcoming events)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS events (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  title         VARCHAR(200) NOT NULL,
  description   TEXT,
  event_date    DATE NOT NULL,
  location      VARCHAR(150)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: admin_users (Admin Dashboard login — supports multiple
-- admin accounts, managed from Admin Dashboard > Manage Admins)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- SAMPLE / STARTER DATA
-- ============================================================

INSERT INTO gallery_images (title, category, file_path, caption, uploaded_at) VALUES
  ('Morning Assembly', 'School Life', 'assets/images/gallery/school-life-1.svg', 'Pupils gathering for morning assembly.', '2026-01-15'),
  ('Swimming Lessons', 'Sports & Swimming', 'assets/images/gallery/sports-1.svg', 'Pupils enjoying a swimming lesson in one of our two pools.', '2026-01-15'),
  ('ICT Laboratory', 'ICT & Academics', 'assets/images/gallery/ict-1.svg', 'Learners in the ICT Laboratory.', '2026-01-15'),
  ('Prize Giving Day', 'Events & Celebrations', 'assets/images/gallery/events-1.svg', 'Celebrating our top performers at Prize Giving Day.', '2026-01-15'),
  ('Classroom Learning', 'School Life', 'assets/images/gallery/school-life-2.svg', 'A conducive classroom environment.', '2026-01-15'),
  ('Sports Day', 'Sports & Swimming', 'assets/images/gallery/sports-2.svg', 'Athletics and sports day activities.', '2026-01-15');

INSERT INTO staff (full_name, role_title, bio, photo_path, display_order) VALUES
  ('Head Teacher', 'Head Teacher', "Leads the Mbuya Parents' School community with a commitment to holistic, values-driven education.", 'assets/images/staff/headteacher.svg', 1),
  ('Deputy Head Teacher', 'Deputy Head Teacher (Academics)', 'Oversees the academic programme from Kindergarten to Primary Seven.', 'assets/images/staff/deputy.svg', 2),
  ('Director of Studies', 'Director of Studies', 'Coordinates curriculum delivery and examination performance.', 'assets/images/staff/dos.svg', 3);

INSERT INTO news_posts (title, slug, category, excerpt, body, cover_image, is_published, published_at) VALUES
  ('Another Year of Outstanding PLE Results',
   'outstanding-ple-results', 'Academics',
   'Our Primary Seven candidates have once again excelled in the Primary Leaving Examinations.',
   "We are proud to announce that our Primary Seven candidates have once again put up an outstanding performance in the Primary Leaving Examinations (PLE), standing out among the top performers in Kampala District and the country as a whole. This continued excellence is a testament to the dedication of our teachers, the support of our parents, and the hard work of our learners. Congratulations to the Class of the Year!",
   'assets/images/news/news-1.svg', 1, '2026-01-20'),
  ('Prize Giving Day Highlights',
   'prize-giving-day-highlights', 'Events',
   'A colourful celebration honouring academic, sporting and talent achievements.',
   "Our annual Prize Giving Day brought together pupils, parents, staff and friends of the school for a colourful celebration of achievement. Awards were given across academics, sports, music, dance and drama, and general conduct. We thank all our parents for the continued partnership in nurturing well-rounded learners.",
   'assets/images/news/news-2.svg', 1, '2025-12-05'),
  ('Preparing Your Child for a New Term',
   'preparing-child-new-term', 'Parent Corner',
   'Simple tips for parents to help learners settle in quickly at the start of term.',
   "A new term brings fresh excitement and a few nerves too. Here are a few simple tips: establish a consistent sleep and morning routine a few days before school opens; involve your child in packing their bag and labelling items; talk positively about school; and keep communication open with your child's class teacher during the first weeks.",
   'assets/images/news/news-3.svg', 1, '2025-08-28'),
  ('Two Swimming Pools, One Great Learning Experience',
   'swimming-pools-learning-experience', 'School News',
   'A look at how our swimming programme builds confidence, discipline and fitness.',
   "Mbuya Parents' School is proud to offer two swimming pools as part of our sports facilities. Swimming lessons are part of our co-curricular programme, helping learners build confidence, discipline, and physical fitness alongside their academic growth.",
   'assets/images/news/news-4.svg', 1, '2025-06-10');

INSERT INTO events (title, description, event_date, location) VALUES
  ('Term II Opening Day', 'All pupils report back to school for the beginning of Term II.', '2026-09-01', 'School Campus, Mbuya'),
  ('Inter-House Sports Gala', 'Annual inter-house athletics and swimming competitions.', '2026-09-19', 'School Sports Grounds & Pools'),
  ('Prize Giving Day', 'End of year celebration recognising outstanding pupils.', '2026-11-28', 'School Main Hall'),
  ('Parents'' Evening — Term II', 'An opportunity for parents to meet class teachers and review learner progress.', '2026-10-10', 'School Main Hall'),
  ('Cultural Gala Day', 'A celebration of Uganda''s diverse cultures through music, dance and drama.', '2026-10-24', 'School Sports Grounds');

-- Default admin login — username: admin, password: MbuyaAdmin@2026
-- CHANGE THIS PASSWORD after your first login (Admin Dashboard > Change
-- Password). Add more admin accounts any time from Admin Dashboard >
-- Manage Admins — no need to edit this file by hand.
INSERT INTO admin_users (username, password_hash) VALUES
  ('admin', '$2y$10$IpDCpnBH3TuqRsg5FJwGy.Q1V04rNpigNzW8NtWru/9PQG85uWV22');
