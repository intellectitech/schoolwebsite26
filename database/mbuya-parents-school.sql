CREATE DATABASE IF NOT EXISTS mbuya_parents
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mbuya_parents;

CREATE TABLE IF NOT EXISTS gallery_categories (
  category_id   INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  slug          VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS gallery_images (
  image_id      INT AUTO_INCREMENT PRIMARY KEY,
  category_id   INT,
  title         VARCHAR(150) NOT NULL,
  file_path     VARCHAR(255) NOT NULL,
  caption       VARCHAR(255),
  uploaded_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES gallery_categories(category_id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS staff (
  staff_id      INT AUTO_INCREMENT PRIMARY KEY,
  full_name     VARCHAR(150) NOT NULL,
  role_title    VARCHAR(150) NOT NULL,
  bio           TEXT,
  photo_path    VARCHAR(255),
  display_order INT DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS news_categories (
  category_id   INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  slug          VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS news_posts (
  post_id       INT AUTO_INCREMENT PRIMARY KEY,
  category_id   INT,
  author_id     INT,
  title         VARCHAR(200) NOT NULL,
  slug          VARCHAR(220) NOT NULL UNIQUE,
  excerpt       VARCHAR(300),
  body          TEXT NOT NULL,
  cover_image   VARCHAR(255),
  is_published  TINYINT(1) DEFAULT 1,
  published_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES news_categories(category_id)
    ON DELETE SET NULL,
  FOREIGN KEY (author_id) REFERENCES staff(staff_id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admissions_inquiries (
  inquiry_id      INT AUTO_INCREMENT PRIMARY KEY,
  parent_name     VARCHAR(150) NOT NULL,
  parent_email    VARCHAR(150) NOT NULL,
  parent_phone    VARCHAR(50) NOT NULL,
  child_name      VARCHAR(150) NOT NULL,
  desired_class   VARCHAR(50) NOT NULL,
  message         TEXT,
  submitted_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  status          ENUM('new','contacted','enrolled','closed') DEFAULT 'new'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contact_messages (
  message_id    INT AUTO_INCREMENT PRIMARY KEY,
  full_name     VARCHAR(150) NOT NULL,
  email         VARCHAR(150) NOT NULL,
  subject       VARCHAR(200),
  message       TEXT NOT NULL,
  submitted_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  is_read       TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS events (
  event_id      INT AUTO_INCREMENT PRIMARY KEY,
  title         VARCHAR(200) NOT NULL,
  description   TEXT,
  event_date    DATE NOT NULL,
  location      VARCHAR(150)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS admin_users (
  admin_id      INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


INSERT INTO gallery_categories (name, slug) VALUES
  ('School Life', 'school-life'),
  ('Sports & Swimming', 'sports-swimming'),
  ('ICT & Academics', 'ict-academics'),
  ('Events & Celebrations', 'events-celebrations');

INSERT INTO gallery_images (category_id, title, file_path, caption) VALUES
  (1, 'Morning Assembly', 'assets/images/gallery/school-life-1.svg', 'Pupils gathering for morning assembly.'),
  (2, 'Swimming Lessons', 'assets/images/gallery/sports-1.svg', 'Pupils enjoying a swimming lesson in one of our two pools.'),
  (3, 'ICT Laboratory', 'assets/images/gallery/ict-1.svg', 'Learners in the ICT Laboratory.'),
  (4, 'Prize Giving Day', 'assets/images/gallery/events-1.svg', 'Celebrating our top performers at Prize Giving Day.'),
  (1, 'Classroom Learning', 'assets/images/gallery/school-life-2.svg', 'A conducive classroom environment.'),
  (2, 'Sports Day', 'assets/images/gallery/sports-2.svg', 'Athletics and sports day activities.');

INSERT INTO staff (full_name, role_title, bio, photo_path, display_order) VALUES
  ('Head Teacher', 'Head Teacher', 'Leads the Mbuya Parents'' School community with a commitment to holistic, values-driven education.', 'assets/images/staff/headteacher.svg', 1),
  ('Deputy Head Teacher', 'Deputy Head Teacher (Academics)', 'Oversees the academic programme from Kindergarten to Primary Seven.', 'assets/images/staff/deputy.svg', 2),
  ('Director of Studies', 'Director of Studies', 'Coordinates curriculum delivery and examination performance.', 'assets/images/staff/dos.svg', 3);

INSERT INTO news_categories (name, slug) VALUES
  ('School News', 'school-news'),
  ('Academics', 'academics'),
  ('Events', 'events'),
  ('Parent Corner', 'parent-corner');

INSERT INTO news_posts (category_id, author_id, title, slug, excerpt, body, cover_image, published_at) VALUES
  (2, 1, 'Another Year of Outstanding PLE Results',
   'outstanding-ple-results',
   'Our Primary Seven candidates have once again excelled in the Primary Leaving Examinations.',
   'We are proud to announce that our Primary Seven candidates have once again put up an outstanding performance in the Primary Leaving Examinations (PLE), standing out among the top performers in Kampala District and the country as a whole. This continued excellence is a testament to the dedication of our teachers, the support of our parents, and the hard work of our learners. Congratulations to the Class of the Year!',
   'assets/images/news/news-1.svg', '2026-01-20 09:00:00'),
  (3, 1, 'Prize Giving Day Highlights',
   'prize-giving-day-highlights',
   'A colourful celebration honouring academic, sporting and talent achievements.',
   'Our annual Prize Giving Day brought together pupils, parents, staff and friends of the school for a colourful celebration of achievement. Awards were given across academics, sports, music, dance and drama, and general conduct. We thank all our parents for the continued partnership in nurturing well-rounded learners.',
   'assets/images/news/news-2.svg', '2025-12-05 09:00:00'),
  (4, 2, 'Preparing Your Child for a New Term',
   'preparing-child-new-term',
   'Simple tips for parents to help learners settle in quickly at the start of term.',
   'A new term brings fresh excitement and a few nerves too. Here are a few simple tips: establish a consistent sleep and morning routine a few days before school opens; involve your child in packing their bag and labelling items; talk positively about school; and keep communication open with your child''s class teacher during the first weeks.',
   'assets/images/news/news-3.svg', '2025-08-28 09:00:00'),
  (1, 1, 'Two Swimming Pools, One Great Learning Experience',
   'swimming-pools-learning-experience',
   'A look at how our swimming programme builds confidence, discipline and fitness.',
   'Mbuya Parents'' School is proud to offer two swimming pools as part of our sports facilities. Swimming lessons are part of our co-curricular programme, helping learners build confidence, discipline, and physical fitness alongside their academic growth.',
   'assets/images/news/news-4.svg', '2025-06-10 09:00:00');

INSERT INTO events (title, description, event_date, location) VALUES
  ('Term II Opening Day', 'All pupils report back to school for the beginning of Term II.', '2026-09-01', 'School Campus, Mbuya'),
  ('Inter-House Sports Gala', 'Annual inter-house athletics and swimming competitions.', '2026-09-19', 'School Sports Grounds & Pools'),
  ('Prize Giving Day', 'End of year celebration recognising outstanding pupils.', '2026-11-28', 'School Main Hall');


INSERT INTO admin_users (username, password_hash) VALUES
  ('admin', '$2y$10$examplehashreplaceThisWithRealPasswordHash1234567890');
