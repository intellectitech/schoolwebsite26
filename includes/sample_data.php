<?php
/**
 * includes/sample_data.php
 * ---------------------------------------------------------------
 * Fallback content so the site renders nicely even before the
 * database has been imported/connected. Mirrors the structure of
 * the tables in database/mbuya_parents_school.sql.
 * ---------------------------------------------------------------
 */

$SAMPLE_GALLERY = [
    ['title' => 'Morning Assembly', 'file_path' => 'assets/images/gallery/school-life-1.svg', 'caption' => 'Pupils gathering for morning assembly.', 'category' => 'School Life'],
    ['title' => 'Swimming Lessons', 'file_path' => 'assets/images/gallery/sports-1.svg', 'caption' => 'Pupils enjoying a swimming lesson in one of our two pools.', 'category' => 'Sports & Swimming'],
    ['title' => 'ICT Laboratory', 'file_path' => 'assets/images/gallery/ict-1.svg', 'caption' => 'Learners in the ICT Laboratory.', 'category' => 'ICT & Academics'],
    ['title' => 'Prize Giving Day', 'file_path' => 'assets/images/gallery/events-1.svg', 'caption' => 'Celebrating our top performers at Prize Giving Day.', 'category' => 'Events & Celebrations'],
    ['title' => 'Classroom Learning', 'file_path' => 'assets/images/gallery/school-life-2.svg', 'caption' => 'A conducive classroom environment.', 'category' => 'School Life'],
    ['title' => 'Sports Day', 'file_path' => 'assets/images/gallery/sports-2.svg', 'caption' => 'Athletics and sports day activities.', 'category' => 'Sports & Swimming'],
];

$SAMPLE_NEWS = [
    [
        'id' => 1,
        'slug' => 'outstanding-ple-results',
        'title' => 'Another Year of Outstanding PLE Results',
        'excerpt' => 'Our Primary Seven candidates have once again excelled in the Primary Leaving Examinations.',
        'body' => "We are proud to announce that our Primary Seven candidates have once again put up an outstanding performance in the Primary Leaving Examinations (PLE), standing out among the top performers in Kampala District and the country as a whole. This continued excellence is a testament to the dedication of our teachers, the support of our parents, and the hard work of our learners. Congratulations to the Class of the Year!",
        'cover_image' => 'assets/images/news/news-1.svg',
        'category' => 'Academics',
        'published_at' => '2026-01-20',
    ],
    [
        'id' => 2,
        'slug' => 'prize-giving-day-highlights',
        'title' => 'Prize Giving Day Highlights',
        'excerpt' => 'A colourful celebration honouring academic, sporting and talent achievements.',
        'body' => "Our annual Prize Giving Day brought together pupils, parents, staff and friends of the school for a colourful celebration of achievement. Awards were given across academics, sports, music, dance and drama, and general conduct. We thank all our parents for the continued partnership in nurturing well-rounded learners.",
        'cover_image' => 'assets/images/news/news-2.svg',
        'category' => 'Events',
        'published_at' => '2025-12-05',
    ],
    [
        'id' => 3,
        'slug' => 'preparing-child-new-term',
        'title' => 'Preparing Your Child for a New Term',
        'excerpt' => 'Simple tips for parents to help learners settle in quickly at the start of term.',
        'body' => "A new term brings fresh excitement and a few nerves too. Here are a few simple tips: establish a consistent sleep and morning routine a few days before school opens; involve your child in packing their bag and labelling items; talk positively about school; and keep communication open with your child's class teacher during the first weeks.",
        'cover_image' => 'assets/images/news/news-3.svg',
        'category' => 'Parent Corner',
        'published_at' => '2025-08-28',
    ],
    [
        'id' => 4,
        'slug' => 'swimming-pools-learning-experience',
        'title' => 'Two Swimming Pools, One Great Learning Experience',
        'excerpt' => 'A look at how our swimming programme builds confidence, discipline and fitness.',
        'body' => "Mbuya Parents' School is proud to offer two swimming pools as part of our sports facilities. Swimming lessons are part of our co-curricular programme, helping learners build confidence, discipline, and physical fitness alongside their academic growth.",
        'cover_image' => 'assets/images/news/news-4.svg',
        'category' => 'School News',
        'published_at' => '2025-06-10',
    ],
];

$SAMPLE_EVENTS = [
    ['title' => 'Term II Opening Day', 'event_date' => '2026-09-01', 'location' => 'School Campus, Mbuya'],
    ['title' => 'Inter-House Sports Gala', 'event_date' => '2026-09-19', 'location' => 'School Sports Grounds & Pools'],
    ['title' => 'Prize Giving Day', 'event_date' => '2026-11-28', 'location' => 'School Main Hall'],
];

$SAMPLE_STAFF = [
    ['full_name' => 'Head Teacher', 'role_title' => 'Head Teacher', 'bio' => "Leads the Mbuya Parents' School community with a commitment to holistic, values-driven education."],
    ['full_name' => 'Deputy Head Teacher', 'role_title' => 'Deputy Head Teacher (Academics)', 'bio' => 'Oversees the academic programme from Kindergarten to Primary Seven.'],
    ['full_name' => 'Director of Studies', 'role_title' => 'Director of Studies', 'bio' => 'Coordinates curriculum delivery and examination performance.'],
];
