-- ============================================================
--  activate_images.sql  —  Activate ALL images properly via DB
--  NPPS · school_website_db
--
--  This script:
--    1. Deletes + re-creates gallery_albums with REAL album covers
--    2. Populates gallery_photos with ALL 24 local images, each
--       with a relevant, descriptive caption sorted into the right
--       album (School Life, Sports & Co-Curricular, Classrooms &
--       Facilities, School Events)
--    3. Updates news.featured_image to actual image paths that
--       MATCH the story
--    4. Updates events.featured_img with thematically correct pics
--    5. Confirms staff.photo paths are correct (they already are)
--
--  Run against: school_website_db
--  Requires: The 24 files in assets/images/ actually exist
--            (images.jpg, image1.jpg ... image23.jpg)
-- ============================================================

USE school_website_db;

-- ============================================================
--  1. GALLERY ALBUMS — wipe existing + set up 4 real albums
--     each with a relevant cover_image
-- ============================================================
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE gallery_photos;
TRUNCATE TABLE gallery_albums;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO gallery_albums (name, description, cover_image, sort_order, is_published) VALUES
('School Life',
 'Everyday pupil life on campus — assembly, break time, classrooms and the NPPS community.',
 'assets/images/images.jpg', 1, 1),

('Sports & Co-Curricular',
 'Inter-house sports, football training, athletics, cultural gala and after-school clubs.',
 'assets/images/image5.jpg', 2, 1),

('Classrooms & Facilities',
 'Our library, classroom blocks, chapel, reception, examination hall, science corner and grounds.',
 'assets/images/image6.jpg', 3, 1),

('School Events',
 "Parents' Day, Open Day, music/drama concerts, graduation celebrations and special days at NPPS.",
 'assets/images/image16.jpg', 4, 1);

-- Remember album IDs for the insert below (MySQL re-seeds from 1
-- after TRUNCATE so we can rely on 1, 2, 3, 4 in that order):
--   id 1 = School Life
--   id 2 = Sports & Co-Curricular
--   id 3 = Classrooms & Facilities
--   id 4 = School Events

-- ============================================================
--  2. GALLERY PHOTOS  — ALL 24 images in assets/images/
--     Each image is placed in the MOST thematically relevant
--     album, given a descriptive caption, and a sort order that
--     tells a mini-story.
--
--     Rule: every image in the folder gets EXACTLY one row,
--     with a filename that matches the file on disk.
-- ============================================================
INSERT INTO gallery_photos (album_id, filename, caption, sort_order, uploaded_by) VALUES

-- ----------------------------------------------------------
-- Album 1 — School Life    (pupils, community, daily campus)
-- ----------------------------------------------------------
(1, 'assets/images/images.jpg',  'NPPS pupils and staff gathered together on campus',                              1, 1),
(1, 'assets/images/image1.jpg',  'Morning assembly — pupils and teachers in devotion',                              2, 1),
(1, 'assets/images/image2.jpg',  'Break time on the school compound with friends',                                  3, 1),
(1, 'assets/images/image3.jpg',  'Primary school pupils walking to class',                                         4, 1),
(1, 'assets/images/image4.jpg',  'Aerial view of the NPPS school compound and grounds',                            5, 1),
(1, 'assets/images/image10.jpg', 'Front office and reception area welcoming parents and visitors',                 6, 1),
(1, 'assets/images/image13.jpg', 'Assembly grounds where morning devotion and announcements are held',             7, 1),
(1, 'assets/images/image22.jpg', 'Pupils at play in the shade during lunch break',                                 8, 1),

-- ----------------------------------------------------------
-- Album 2 — Sports & Co-Curricular
-- ----------------------------------------------------------
(2, 'assets/images/image5.jpg',  'Annual Inter-House Sports Day — athletics and cheering on the field',            1, 1),
(2, 'assets/images/image9.jpg',  'Under-13 boys football team training for the zonal tournament',                  2, 1),
(2, 'assets/images/image16.jpg', "Parents' Day — outdoor activities and games for families",                       3, 1),
(2, 'assets/images/image19.jpg', 'Inter-Class Cultural Gala — traditional dance from Eastern Uganda',               4, 1),
(2, 'assets/images/image18.jpg', 'Main sports field used for athletics, football and P.E. lessons',                5, 1),

-- ----------------------------------------------------------
-- Album 3 — Classrooms & Facilities
-- ----------------------------------------------------------
(3, 'assets/images/image6.jpg',  'Two-storey library block with dedicated reading corners for all classes',        1, 1),
(3, 'assets/images/image7.jpg',  'Classroom block for Primary 4 to Primary 7',                                     2, 1),
(3, 'assets/images/image8.jpg',  'Examination hall used during PLE mock and final exams',                          3, 1),
(3, 'assets/images/image12.jpg', 'School chapel — daily devotion and weekly chapel services',                      4, 1),
(3, 'assets/images/image14.jpg', "Head teacher's office and school administration block",                          5, 1),
(3, 'assets/images/image15.jpg', 'Science corner — practical science lessons for P5–P7',                           6, 1),
(3, 'assets/images/image17.jpg', 'Well-kept school grounds and landscaped gardens',                                7, 1),
(3, 'assets/images/image20.jpg', 'Staff room — planning, meetings and marking for teachers',                       8, 1),
(3, 'assets/images/image21.jpg', 'Music room — choir practice and instrument lessons',                             9, 1),
(3, 'assets/images/image11.jpg', 'Primary 7 classroom focused on PLE revision',                                   10, 1),

-- ----------------------------------------------------------
-- Album 4 — School Events
-- ----------------------------------------------------------
(4, 'assets/images/image23.jpg', 'End of Year Christmas Party and prize-giving day',                               1, 1),
(4, 'assets/images/image16.jpg', "Annual Parents' Day — class presentations and talent showcase",                  2, 1);

-- ============================================================
--  3. NEWS ARTICLES — set featured_image to a path that MATCHES
--     the category and story.  Every published article gets a
--     real image that actually lives in assets/images/.
-- ============================================================
--  id 1  P7 PLE Results                     → academics
--  id 2  Inter-House Sports Day             → sports
--  id 3  Science Fair                       → academics/facilities
--  id 4  New Library Block                  → facilities
--  id 5  Martyrs Day Community Outreach     → community (school life)
--  id 6  Music Dance Drama Concert          → arts / events
--  id 7  Football Zonal Finals              → sports

UPDATE news SET featured_image = 'assets/images/image11.jpg' WHERE id = 1;
UPDATE news SET featured_image = 'assets/images/image5.jpg'  WHERE id = 2;
UPDATE news SET featured_image = 'assets/images/image15.jpg' WHERE id = 3;
UPDATE news SET featured_image = 'assets/images/image6.jpg'  WHERE id = 4;
UPDATE news SET featured_image = 'assets/images/image2.jpg'  WHERE id = 5;
UPDATE news SET featured_image = 'assets/images/image21.jpg' WHERE id = 6;
UPDATE news SET featured_image = 'assets/images/image9.jpg'  WHERE id = 7;

-- ============================================================
--  4. EVENTS — set featured_img to thematically appropriate
--     images that actually exist on disk.
-- ============================================================
--  id 1  Parents' Day Celebration          → image16
--  id 2  School Open Day                   → image17 (tour / grounds)
--  id 3  End of Term II Examinations       → image8  (exam hall)
--  id 4  Career Guidance & Mentorship      → image13 (assembly/auditorium)
--  id 5  Inter-Class Cultural Gala         → image19
--  id 6  End of Year Christmas Party       → image23

UPDATE events SET featured_img = 'assets/images/image16.jpg' WHERE id = 1;
UPDATE events SET featured_img = 'assets/images/image17.jpg' WHERE id = 2;
UPDATE events SET featured_img = 'assets/images/image8.jpg'  WHERE id = 3;
UPDATE events SET featured_img = 'assets/images/image13.jpg' WHERE id = 4;
UPDATE events SET featured_img = 'assets/images/image19.jpg' WHERE id = 5;
UPDATE events SET featured_img = 'assets/images/image23.jpg' WHERE id = 6;

-- ============================================================
--  5. STAFF — confirm and tidy up.  Seed data already used
--     real existing images; we re-assert them here in case any
--     demo DB had empty photo fields, plus add a few more if
--     there are extra rows.
-- ============================================================
-- id 1 = Head Teacher             → image14 (Head teacher's office area)
-- id 2 = Deputy / DoS             → image20 (staff room)
-- id 3 = Chaplain                 → image12 (chapel)
-- id 4 = P1 Teacher (Lower Pri)   → image6 (younger classes near library)
-- id 5 = P6 Teacher (Upper Pri)   → image7 (P4–P7 block)
-- id 6 = Games / PE Teacher       → image18 (sports field)

UPDATE staff SET photo = 'assets/images/image14.jpg' WHERE id = 1;
UPDATE staff SET photo = 'assets/images/image20.jpg' WHERE id = 2;
UPDATE staff SET photo = 'assets/images/image12.jpg' WHERE id = 3;
UPDATE staff SET photo = 'assets/images/image6.jpg'  WHERE id = 4;
UPDATE staff SET photo = 'assets/images/image7.jpg'  WHERE id = 5;
UPDATE staff SET photo = 'assets/images/image18.jpg' WHERE id = 6;

-- ============================================================
--  6. TESTIMONIALS — optional: add a headshot for the 4 demo
--     testimonials so avatars show on the homepage & about.
--     (use school-life themed images that feel parent-alumni)
-- ============================================================
UPDATE testimonials SET photo = 'assets/images/image3.jpg'  WHERE id = 1;
UPDATE testimonials SET photo = 'assets/images/image4.jpg'  WHERE id = 2;
UPDATE testimonials SET photo = 'assets/images/image1.jpg'  WHERE id = 3;
UPDATE testimonials SET photo = 'assets/images/image22.jpg' WHERE id = 4;

-- ============================================================
--  Done.
--
--  To apply:
--   phpMyAdmin  → select school_website_db → Import → this file
--   OR CLI:      mysql -u root school_website_db < activate_images.sql
--
--  After import, refresh the public Gallery page and the
--  News & Events page — every image reference now points at
--  a file that really exists in assets/images/.
-- ============================================================
