-- ============================================================
--  seed_data.sql — Content for Namugongo Parents' Primary School
--  Run AFTER school_website_db.sql AND db_fixes.sql
--  Every table gets at least 3 real, NPPS-specific rows.
-- ============================================================

USE school_website_db;

-- ------------------------------------------------------------
-- admin_users  (3 accounts — one per role the panel supports)
-- Passwords below are the PLAIN TEXT you log in with; the column
-- stores a bcrypt hash of it. Change these after first login.
--   admin@npps.ac.ug   / NppsAdmin@2026   (super_admin)
--   editor@npps.ac.ug  / NppsEditor@2026  (editor)
--   staff@npps.ac.ug   / NppsStaff@2026   (staff)
-- ------------------------------------------------------------
INSERT INTO admin_users (name, email, password, role, profile_photo, is_active) VALUES
('Josephine Nakabuye',    'admin@npps.ac.ug',  '$2y$10$MVoOtq6RXod8eCB7Rm3..u8FivA4/KAKSxdru0tO5WhXDK1POxMFq', 'super_admin', '', 1),
('Peter Ssemwogerere',    'editor@npps.ac.ug', '$2y$10$qPcYjp/Tu4Pn8oHKasRzg.RLtj9c3IYEO3N5nxu2xyjgDP3TzgC8.', 'editor',      '', 1),
('Grace Auma',            'staff@npps.ac.ug',  '$2y$10$I/NicKqNHcwKeDEMU/.qYereMAUq/PgmVidAG/lrEefbk4rXFFGXW', 'staff',       '', 1);

-- ------------------------------------------------------------
-- school_info settings
-- ------------------------------------------------------------
INSERT INTO school_info (setting_key, setting_value, description, updated_by) VALUES
('school_name',      "Namugongo Parents' Primary School", 'Display name', 1),
('school_short_name','NPPS', 'Short form used in badges/labels', 1),
('motto',            'Knowledge, Character, Faith', 'School motto', 1),
('school_phone',     '+256 703 072 573', 'Phone', 1),
('school_email',     'info@npps.ac.ug', 'Email', 1),
('school_address',   'Plot 245, Namugongo Road, Kira Municipality, Wakiso, Uganda', 'Address', 1),
('office_hours',     'Monday - Friday, 8:00 AM - 5:00 PM', 'Hours', 1),
('hero_title',       "Welcome to Namugongo Parents' Primary School", 'Hero', 1),
('hero_subtitle',    'Nurturing Every Child Through Excellence In Education, Integrity, And Innovation', 'Hero sub', 1),
('founded_year',     '25+', 'Stat: years of operation (shown on homepage)', 1),
('total_students',   '1200+', 'Stat: current enrolment', 1),
('stat_pass_rate',   '96%', 'Stat: PLE Division 1&2 rate', 1),
('stat_teachers',    '48', 'Stat: qualified teaching staff', 1),
('facebook_url',     '#', 'Social', 1),
('twitter_url',      '#', 'Social', 1),
('instagram_url',    '#', 'Social', 1),
('vision_text',      'To be a leading primary school in Uganda, nurturing confident, disciplined and academically excellent learners equipped for secondary school and for life.', 'About', 1),
('mission_text',     'To provide holistic, Christ-centred primary education that develops the intellectual, social, physical and spiritual potential of every child in a safe and caring environment.', 'About', 1),
('about_intro',      "Namugongo Parents' Primary School (NPPS) is a day primary school situated minutes from the Namugongo Martyrs Shrines. Since 1998 we have grown from a small parent-founded nursery into a full Baby Class-to-P7 school known across Kira Municipality for strong PLE results and a warm, disciplined learning community.", 'About', 1)
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- ------------------------------------------------------------
-- departments  (4)
-- ------------------------------------------------------------
INSERT INTO departments (name, description, head_of_dept) VALUES
('Lower Primary (Baby Class - P3)', 'Early literacy, numeracy and play-based learning for our youngest learners.', 'Sarah Nansubuga'),
('Upper Primary (P4 - P7)', 'Full NCDC primary curriculum delivery and structured PLE preparation.', 'Peter Ssemwogerere'),
('Religious Education & Pastoral Care', 'Chaplaincy, moral instruction, guidance and counselling for pupils of all faiths.', 'Fr. Emmanuel Kirumira'),
('Sports & Co-Curricular', 'Athletics, ball games, music/dance/drama and clubs.', 'David Ochieng');

-- ------------------------------------------------------------
-- staff  (6 — 3 management, 3 class/subject teachers)
-- ------------------------------------------------------------
INSERT INTO staff (department_id, first_name, last_name, title, role, subjects, qualification, photo, is_managemnet, sort_order, is_active, bio, email) VALUES
(2, 'Josephine', 'Nakabuye',   'Mrs.', 'Head Teacher',
 'School Leadership', 'M.Ed (Educational Management), Makerere University', 'assets/images/image14.jpg', 1, 1, 1,
 'Josephine has led NPPS since 2015, growing the school\'s PLE performance and overseeing the construction of the current library block.', 'headteacher@npps.ac.ug'),
(2, 'Peter', 'Ssemwogerere', 'Mr.', 'Deputy Head Teacher / Director of Studies',
 'Mathematics, Science', 'B.Ed (Science), Kyambogo University', 'assets/images/image20.jpg', 1, 2, 1,
 'Peter oversees the academic timetable, staff development and examination standards across P1-P7.', 'deputy@npps.ac.ug'),
(3, 'Fr. Emmanuel', 'Kirumira', 'Fr.', 'Chaplain & Head of Religious Education',
 'Christian Religious Education', 'Diploma in Theology; B.A Religious Studies, Uganda Martyrs University', 'assets/images/image12.jpg', 1, 3, 1,
 'Father Emmanuel leads morning devotion, chapel services and welfare support for pupils and families of all denominations.', 'chaplain@npps.ac.ug'),
(1, 'Sarah', 'Nansubuga', 'Ms.', 'P1 Class Teacher',
 'Literacy, Numeracy', 'Grade V Certificate, Namutamba PTC', 'assets/images/image6.jpg', 0, 4, 1,
 'Sarah specialises in early-years literacy and has taught Baby Class through P3 at NPPS for eight years.', 'snansubuga@npps.ac.ug'),
(2, 'Betty', 'Nalubega', 'Mrs.', 'P6 Class Teacher',
 'English, Social Studies', 'B.Ed (Arts), Makerere University', 'assets/images/image7.jpg', 0, 5, 1,
 'Betty coordinates the P6/P7 PLE preparation programme and the school debate club.', 'bnalubega@npps.ac.ug'),
(4, 'David', 'Ochieng', 'Mr.', 'Games & Physical Education Teacher',
 'Physical Education', 'Diploma in Sports Science, Makerere University Business School', 'assets/images/image18.jpg', 0, 6, 1,
 'David coaches the school\'s athletics and football teams and runs the Wednesday co-curricular clubs programme.', 'dochieng@npps.ac.ug');

-- ------------------------------------------------------------
-- subjects  (6)
-- ------------------------------------------------------------
INSERT INTO subjects (department_id, name, level, is_compulsory, description, sort_order) VALUES
(1, 'Literacy & Reading', '', 1, 'Phonics, reading fluency and handwriting for Baby Class through P3.', 1),
(1, 'Numeracy', '', 1, 'Foundational number work, shapes and early problem solving for lower primary.', 2),
(2, 'English Language', '', 1, 'Grammar, composition and comprehension aligned to the NCDC thematic curriculum.', 3),
(2, 'Mathematics', '', 1, 'Number, measurement, geometry and data handling through to P7 PLE level.', 4),
(2, 'Integrated Science', '', 1, 'Health, environment and basic science concepts from P4 to P7.', 5),
(2, 'Social Studies', '', 1, 'Uganda\'s geography, history and civic education.', 6);

-- ------------------------------------------------------------
-- news_categories
-- ------------------------------------------------------------
INSERT INTO news_categories (name, slug, color) VALUES
('Academic', 'academic', '#1565C0'),
('Sports', 'sports', '#16a34a'),
('Facilities', 'facilities', '#f59e0b'),
('Community', 'community', '#9333ea'),
('Arts', 'arts', '#db2777');

-- ------------------------------------------------------------
-- news  (author_id = 1, Josephine Nakabuye)
-- ------------------------------------------------------------
INSERT INTO news (category_id, title, slug, excerpt, body, featured_image, author_id, views, is_published, is_featured, published_at) VALUES
(1, 'P7 Candidates Graduate With Excellent PLE Results', 'p7-candidates-graduate-with-excellent-ple-results',
 'Our 2025 Primary Seven candidates posted a 96% Division One and Two pass rate, the best in the school\'s history.',
 'Our 2025 Primary Seven candidates have once again made us proud, posting a 96% Division One and Two pass rate in the Primary Leaving Examinations — the best result in the school\'s history. Congratulations to all our graduates, their teachers, and the parents who supported them throughout the year.',
 'assets/images/image11.jpg', 1, 0, 1, 1, '2026-07-18 09:00:00'),
(2, 'Annual Inter-House Sports Day a Big Success', 'annual-inter-house-sports-day-a-big-success',
 'Green House emerged champions after a thrilling day of athletics, football, netball and tug-of-war.',
 'The much-anticipated annual sports day was a huge success! Green House emerged champions after a thrilling day of athletics, football, netball, and tug-of-war on the school field. Parents and guardians turned out in large numbers to cheer on the four houses.',
 'assets/images/image5.jpg', 1, 0, 1, 0, '2026-07-10 09:00:00'),
(1, 'Inaugural Science & Innovation Fair Sparks Curiosity', 'inaugural-science-and-innovation-fair-sparks-curiosity',
 'P5-P7 pupils showcased creative science projects, from rainwater harvesting models to simple circuits.',
 'Pupils in Primary Five through Seven showcased incredible creativity and scientific thinking at our first annual Science & Innovation Fair. Projects ranged from rainwater harvesting models to simple electrical circuits, judged by a panel of teachers and two visiting science teachers from Namugongo College.',
 'assets/images/image15.jpg', 1, 0, 1, 0, '2026-06-28 09:00:00'),
(3, 'New Library Block Now Open to Pupils', 'new-library-block-now-open-to-pupils',
 'We are delighted to open our new two-storey library featuring reading corners and over 4,000 books.',
 'We are delighted to announce the opening of our new two-storey library block, featuring dedicated reading corners for lower and upper primary, over 4,000 books, and a small computer research area. The block was funded jointly by the school and the Parents\' Association.',
 'assets/images/image6.jpg', 1, 0, 1, 0, '2026-06-15 09:00:00'),
(4, 'NPPS Pupils Take Part in Martyrs Day Community Outreach', 'npps-pupils-take-part-in-martyrs-day-community-outreach',
 'Ahead of the 3 June celebrations at the nearby Shrines, our pupils joined a school clean-up and gift drive.',
 'In the week leading up to the 3 June Martyrs Day celebrations at the nearby Namugongo Shrines, our P5-P7 pupils joined a community clean-up along Namugongo Road and donated books and scholastic materials to a neighbouring nursery school. It is one of the ways NPPS tries to connect classroom learning with the community we sit in.',
 'assets/images/image7.jpg', 1, 0, 1, 0, '2026-06-05 09:00:00'),
(5, 'Term Two Music, Dance & Drama Concert Delights Parents', 'term-two-music-dance-and-drama-concert-delights-parents',
 'Our talented pupils put on an unforgettable performance featuring choir, traditional dance and drama sketches.',
 'Our talented pupils put on an unforgettable performance at the Term Two concert, featuring the school choir, traditional dance from several regions of Uganda, and drama sketches written by the P6 English class.',
 'assets/images/image21.jpg', 1, 0, 1, 0, '2026-05-22 09:00:00'),
(2, 'School Football Team Reaches Zonal Finals', 'school-football-team-reaches-zonal-finals',
 'The NPPS Under-13 boys team beat two Kira-zone schools to reach the finals for the first time.',
 'The NPPS Under-13 boys football team beat two other Kira-zone primary schools on penalties to reach the zonal finals for the first time in school history. Coach David Ochieng credited the Wednesday co-curricular training sessions introduced this year.',
 'assets/images/image9.jpg', 1, 0, 1, 0, '2026-05-02 09:00:00');

-- ------------------------------------------------------------
-- events
-- ------------------------------------------------------------
INSERT INTO events (title, description, location, event_date, start_time, end_time, featured_img, is_published, created_by) VALUES
("Annual Parents' Day Celebration",
 "Join us for a special day celebrating our pupils' achievements. Featuring class presentations, a talent showcase, and one-on-one parent-teacher conversations.",
 'School Main Hall', '2026-08-15', '2026-08-15 09:00:00', '2026-08-15 15:00:00', 'assets/images/image16.jpg', 1, 1),
('School Open Day - New Intake',
 'Prospective parents and pupils are invited to tour our campus, meet our teachers, and learn more about our Baby Class-to-P7 programme ahead of Term One intake.',
 'School Premises, Namugongo Road', '2026-09-05', '2026-09-05 08:00:00', '2026-09-05 17:00:00', 'assets/images/image17.jpg', 1, 1),
('End of Term II Examinations',
 'End of Term II examinations run from 22 September to 3 October for P1-P7. Pupils are encouraged to prepare adequately and arrive on time each day.',
 'All Classrooms', '2026-09-22', '2026-09-22 08:00:00', '2026-09-22 12:00:00', 'assets/images/image8.jpg', 1, 1),
('Career Guidance & Mentorship Day',
 'Guest speakers from various professions will inspire and guide our P5-P7 pupils as they begin thinking about secondary school and future career paths.',
 'School Auditorium', '2026-10-10', '2026-10-10 10:00:00', '2026-10-10 16:00:00', 'assets/images/image18.jpg', 1, 1),
('Inter-Class Cultural Gala',
 "Celebrate Uganda's rich cultural heritage! Each class represents a different region with traditional dance, food, dress and storytelling.",
 'School Sports Ground', '2026-10-28', '2026-10-28 09:00:00', '2026-10-28 18:00:00', 'assets/images/image19.jpg', 1, 1),
('End of Year Christmas Party',
 'Celebrate the end of a successful academic year with food, music, games, and gift-giving. All pupils and parents are welcome to attend.',
 'School Main Hall', '2026-12-05', '2026-12-05 11:00:00', '2026-12-05 16:00:00', 'assets/images/image23.jpg', 1, 1);

-- ------------------------------------------------------------
-- testimonials  (4)
-- ------------------------------------------------------------
INSERT INTO testimonials (author_name, author_role, photo, content, rating, sort_order, is_published) VALUES
('Sarah Nakato', 'Parent, P4', '', 'The teachers here truly care about every child. My daughter has grown so much in confidence and in her reading since joining NPPS.', 5, 1, 1),
('James Okello', 'Parent, P2 & P6', '', 'Excellent facilities and a safe, disciplined environment. I have two children here and I recommend this school to any parent in Kira.', 5, 2, 1),
('Grace Nabirye', 'Alumna, Class of 2020', '', "NPPS gave me the foundation I needed to succeed at secondary school. I still remember my P7 teachers' encouragement.", 5, 3, 1),
('Moses Kiwanuka', 'Parent, P1', '', 'From the very first day, the Baby Class and P1 teachers made the transition easy for my son. Communication with parents is excellent.', 4, 4, 1);

-- ------------------------------------------------------------
-- gallery  (3 albums)
-- ------------------------------------------------------------
INSERT INTO gallery_albums (name, description, cover_image, sort_order, is_published) VALUES
('School Life', 'Campus and everyday pupil life at NPPS', 'assets/images/images.jpg', 1, 1),
('Sports & Co-Curricular', 'Sports day, football, and club activities', 'assets/images/image5.jpg', 2, 1),
('Classrooms & Facilities', 'Our classrooms, library and school grounds', 'assets/images/image6.jpg', 3, 1);

SET @album_school_life = LAST_INSERT_ID();
SET @album_sports = @album_school_life + 1;
SET @album_facilities = @album_school_life + 2;

INSERT INTO gallery_photos (album_id, filename, caption, sort_order, uploaded_by) VALUES
(@album_school_life, 'assets/images/images.jpg', 'NPPS pupils and staff', 1, 1),
(@album_school_life, 'assets/images/image1.jpg', 'Morning assembly', 2, 1),
(@album_school_life, 'assets/images/image2.jpg', 'Break time', 3, 1),
(@album_school_life, 'assets/images/image3.jpg', 'Pupils on campus', 4, 1),
(@album_school_life, 'assets/images/image4.jpg', 'School compound', 5, 1),
(@album_school_life, 'assets/images/image10.jpg', 'Front office and reception', 6, 1),
(@album_school_life, 'assets/images/image13.jpg', 'Assembly grounds', 7, 1),
(@album_school_life, 'assets/images/image22.jpg', 'Pupils at play', 8, 1),
(@album_sports, 'assets/images/image5.jpg', 'Inter-house sports day', 1, 1),
(@album_sports, 'assets/images/image9.jpg', 'Football training', 2, 1),
(@album_sports, 'assets/images/image16.jpg', "Parents' Day activities", 3, 1),
(@album_sports, 'assets/images/image19.jpg', 'Cultural gala performance', 4, 1),
(@album_sports, 'assets/images/image23.jpg', 'End of year celebrations', 5, 1),
(@album_facilities, 'assets/images/image6.jpg', 'Library block', 1, 1),
(@album_facilities, 'assets/images/image7.jpg', 'Classroom block', 2, 1),
(@album_facilities, 'assets/images/image8.jpg', 'Examination hall', 3, 1),
(@album_facilities, 'assets/images/image12.jpg', 'Chapel and assembly area', 4, 1),
(@album_facilities, 'assets/images/image14.jpg', "Head teacher's office", 5, 1),
(@album_facilities, 'assets/images/image15.jpg', 'Science corner', 6, 1),
(@album_facilities, 'assets/images/image17.jpg', 'School grounds', 7, 1),
(@album_facilities, 'assets/images/image18.jpg', 'Sports field', 8, 1),
(@album_facilities, 'assets/images/image20.jpg', 'Staff room', 9, 1),
(@album_facilities, 'assets/images/image21.jpg', 'Music room', 10, 1),
(@album_facilities, 'assets/images/image11.jpg', 'P7 classroom', 11, 1);

-- ------------------------------------------------------------
-- admission_requirements  (5, level '' = applies to all pupils)
-- ------------------------------------------------------------
INSERT INTO admission_requirements (level, title, description, sort_order) VALUES
('', 'Birth Certificate', 'An original and photocopy of the child\'s birth certificate for verification.', 1),
('', 'Passport Photographs', 'Two recent passport-size photographs of the child.', 2),
('', 'Immunisation Card', 'Up-to-date immunisation record from a recognised health facility.', 3),
('', 'Transfer Letter', 'For pupils transferring from another school: a leaving/transfer letter from the previous school.', 4),
('', 'Report Card / Previous Results', 'Most recent school report card, where applicable, to help us place the child in the right class.', 5);

-- ------------------------------------------------------------
-- admission_documents  (3)
-- ------------------------------------------------------------
INSERT INTO admission_documents (title, description, filename, file_size, level, downloads, is_active) VALUES
('P1-P7 Application Form', 'The official NPPS application form for new admissions, Baby Class through Primary Seven.', 'assets/documents/npps-application-form.pdf', '210 KB', 'ALL', 0, 1),
('School Fees Structure', 'Current termly fees structure for all classes, including meals and transport options.', 'assets/documents/npps-fees-structure.pdf', '150 KB', 'ALL', 0, 1),
('Transfer Pupil Checklist', 'A short checklist of documents needed when transferring your child from another primary school.', 'assets/documents/npps-transfer-checklist.pdf', '95 KB', 'ALL', 0, 1);

-- ------------------------------------------------------------
-- faqs  (3 per category)
-- ------------------------------------------------------------
INSERT INTO faqs (question, answer, category, sort_order, is_published) VALUES
('What age does my child need to be to join Baby Class?', 'Children joining Baby Class should be at least 3 years old by the start of Term One. We assess school readiness informally during the admissions interview rather than by date of birth alone.', 'admissions', 1, 1),
('Do you accept mid-year transfers?', 'Yes. We accept transfers into most classes throughout the year, subject to space and a short placement assessment so we can confirm the right class for your child.', 'admissions', 2, 1),
('How do I apply for admission?', 'Visit the school office to collect (or download from this site) an application form, submit it with the required documents, and our admissions office will schedule an assessment and interview date.', 'admissions', 3, 1),
('When are school fees due?', 'Fees are due at the start of each term, on or before the first day of the term. A payment plan can be arranged with the bursar\'s office for parents who need to pay in instalments.', 'fees', 1, 1),
('What is included in the school fees?', 'Tuition, instructional materials, and lunch are included for day scholars. Transport is an optional add-on charged separately based on your pick-up zone.', 'fees', 2, 1),
('Do you offer any fee discounts?', 'We offer a sibling discount for families with more than one child enrolled at NPPS at the same time. Speak to the bursar\'s office for the current rate.', 'fees', 3, 1),
('What curriculum do you follow?', 'We follow the National Curriculum Development Centre (NCDC) thematic curriculum for lower primary and the standard primary curriculum through to Primary Seven, in preparation for PLE.', 'academics', 1, 1),
('What is your average class size?', 'We keep class sizes at around 35-40 pupils per stream so teachers can give individual attention, especially during literacy and numeracy lessons in the lower classes.', 'academics', 2, 1),
('Do you provide extra support for struggling learners?', 'Yes. Class teachers run small-group catch-up sessions during the week, and P5-P7 pupils have access to structured PLE revision support.', 'academics', 3, 1),
('Is NPPS a boarding school?', 'No. NPPS is a day school only — we do not currently offer boarding facilities. Most of our pupils live within Kira Municipality and the wider Namugongo area.', 'boarding', 1, 1),
('Do you provide school transport?', 'Yes, we run school van transport on a limited number of routes around Namugongo, Kira and Sonde. Ask the front office whether your area is covered.', 'boarding', 2, 1),
('Is there a place for pupils to rest during lunch break?', 'Yes, each class block has a shaded rest area, and younger pupils in Baby Class and P1 have a supervised quiet-rest period after lunch.', 'boarding', 3, 1),
('What are your school hours?', 'The school day runs from 7:30 AM to 5:00 PM, Monday to Friday, with the main teaching day ending at 4:00 PM followed by co-curricular clubs.', 'general', 1, 1),
('Are visits to the school allowed before enrolling?', 'Absolutely — we welcome prospective parents to visit any weekday during office hours, or to attend our termly Open Day for a guided tour.', 'general', 2, 1),
('Who do I contact for a specific concern about my child?', "Start with your child's class teacher; the front office can direct you to the Deputy Head Teacher or Head Teacher for anything that needs further attention.", 'general', 3, 1);

-- ------------------------------------------------------------
-- page_content  (3 — editable long-form blocks used on about.php)
-- ------------------------------------------------------------
INSERT INTO page_content (page, section, content, updated_by) VALUES
('about', 'history',
 "Namugongo Parents' Primary School was founded in 1998 by a group of local parents who wanted a nearby, affordable, values-driven alternative for their children. Starting with a single rented classroom and 24 pupils, the school has grown steadily into a full Baby Class-to-Primary-Seven institution with its own permanent campus on Namugongo Road, a library block, and a teaching staff of qualified professionals. Our location, a short distance from the Namugongo Martyrs Shrines, continues to shape a school culture that values faith, service and community alongside academics.",
 1),
('about', 'facilities',
 "Our campus includes classroom blocks for lower and upper primary, a two-storey library, a small computer research room, a chapel used for daily devotion, a sports field used for athletics and football, and a dedicated examination hall used during PLE mock and final exam periods.",
 1),
('admissions', 'welcome_note',
 "Thank you for considering Namugongo Parents' Primary School for your child. Our admissions office is happy to answer questions by phone, email, or in person during office hours — the enquiry form below is usually the fastest way to reach us.",
 1);

-- ------------------------------------------------------------
-- admission_enquiries  (3 demo rows)
-- ------------------------------------------------------------
INSERT INTO admission_enquiries
    (parent_name, parent_phone, parent_email, student_name, entry_level, current_school, ple_aggregate, message, status, admin_notes)
VALUES
('Fiona Namutebi', '+256772345678', 'fnamutebi@example.com', 'Isaac Namutebi', 'P1', '', NULL,
 'Preferred intake: Term One (February)\n\nLooking to enrol my son for next year. Please advise on the assessment date.',
 'new', ''),
('Robert Kaggwa', '+256701987654', 'rkaggwa@example.com', 'Patricia Kaggwa', 'P4', 'Greenfield Primary School', NULL,
 'We are relocating to Kira and would like to transfer our daughter mid-year if a P4 space is available.',
 'contacted', 'Called 15 July — space confirmed for Term 3, awaiting transfer letter.'),
('Diana Achieng', '+256789112233', 'dachieng@example.com', 'Brian Achieng', 'Top Class', '', NULL,
 'My son turns 5 in January and I would like him to join Top Class next year.',
 'enrolled', 'Enrolled — assessment completed 20 June, admission confirmed.');

-- ------------------------------------------------------------
-- contact_messages  (3 demo rows)
-- ------------------------------------------------------------
INSERT INTO contact_messages (name, email, phone, subject, message, ip_addess, is_read, replied_at, created_at) VALUES
('Esther Namusoke', 'enamusoke@example.com', '+256776554433', 'Question about lunch menu',
 'Good afternoon, could you share what is typically served for lunch? My daughter has a peanut allergy and I want to check beforehand.',
 '102.135.20.14', 1, '2026-07-20 10:15:00', '2026-07-19 14:02:00'),
('Tom Byaruhanga', 'tbyaruhanga@example.com', '+256701223344', 'Fees payment confirmation',
 'I made a Term 3 fees payment via mobile money yesterday. Could someone confirm it has been received and applied to my son\'s account?',
 '41.210.144.87', 0, NULL, '2026-07-27 09:40:00'),
('Patience Aciro', 'paciro@example.com', '+256789667788', 'Requesting a school tour',
 'Hello, I am relocating to Kira from Gulu and would like to schedule a tour of the school before the new term starts. What days work best?',
 '105.163.2.201', 0, NULL, '2026-07-29 16:55:00');

-- ------------------------------------------------------------
-- newsletters_subscribers  (3 demo rows)
-- ------------------------------------------------------------
INSERT INTO newsletters_subscribers (email, name, is_confirmed, confirm_token, unsubscribed_at) VALUES
('mkyeyune@example.com', '', 1, 'a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6', NULL),
('jnalwoga@example.com', '', 1, 'b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7', NULL),
('scharles@example.com', '', 0, 'c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8', NULL);

-- ------------------------------------------------------------
-- audit_log  (3 illustrative rows — this table otherwise fills
-- up automatically as admins use the panel)
-- ------------------------------------------------------------
INSERT INTO audit_log (admin_id, action, table_name, record_id, description, ip_address, created_at) VALUES
(1, 'LOGIN', 'admin_users', 1, 'Josephine Nakabuye logged in', '102.135.20.5', '2026-07-29 08:02:00'),
(1, 'INSERT', 'news', 1, 'Created: P7 Candidates Graduate With Excellent PLE Results', '102.135.20.5', '2026-07-18 09:05:00'),
(2, 'UPDATE', 'events', 1, "Updated: Annual Parents' Day Celebration", '102.135.20.9', '2026-07-22 11:30:00');
