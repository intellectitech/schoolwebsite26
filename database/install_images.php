<?php
// Activate all gallery/news/events/staff images by inserting them
// into the database properly. Uses PDO directly to avoid session issues.

$host = 'localhost';
$db   = 'school_website_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

echo "<pre>";
echo "=== Activating images in DB ===\n\n";

// ----------------------------------------------------------
// Step 1: Fix gallery (delete old albums/photos, recreate)
// ----------------------------------------------------------
echo "[1] Resetting gallery_albums & gallery_photos ...\n";
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('DELETE FROM gallery_photos');
$pdo->exec('DELETE FROM gallery_albums');
$pdo->exec('ALTER TABLE gallery_albums AUTO_INCREMENT = 1');
$pdo->exec('ALTER TABLE gallery_photos AUTO_INCREMENT = 1');
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

// --- 4 albums ---
$albums = [
    [1, 'School Life',
        'Everyday pupil life on campus — assembly, break time, classrooms and the NPPS community.',
        'assets/images/images.jpg'],
    [2, 'Sports & Co-Curricular',
        'Inter-house sports, football training, athletics, cultural gala and after-school clubs.',
        'assets/images/image5.jpg'],
    [3, 'Classrooms & Facilities',
        'Library, classroom blocks, chapel, reception, examination hall, science corner and grounds.',
        'assets/images/image6.jpg'],
    [4, 'School Events',
        "Parents' Day, Open Day, music/drama concerts, graduation celebrations and special days at NPPS.",
        'assets/images/image16.jpg'],
];
$stmtA = $pdo->prepare('INSERT INTO gallery_albums (id, name, description, cover_image, sort_order, is_published) VALUES (?,?,?,?,?,1)');
foreach ($albums as $a) { $stmtA->execute($a); }
echo "    Inserted 4 albums.\n";

// --- 25 photos (all 24 files on disk, image16 appears twice on purpose) ---
$photos = [
    // Album 1 — School Life
    [1,1,'assets/images/images.jpg', 'NPPS pupils and staff gathered together on campus',1],
    [2,1,'assets/images/image1.jpg', 'Morning assembly — pupils and teachers in devotion',2],
    [3,1,'assets/images/image2.jpg', 'Break time on the school compound with friends',3],
    [4,1,'assets/images/image3.jpg', 'Primary school pupils walking to class',4],
    [5,1,'assets/images/image4.jpg', 'Aerial view of the NPPS school compound and grounds',5],
    [6,1,'assets/images/image10.jpg','Front office and reception area welcoming parents and visitors',6],
    [7,1,'assets/images/image13.jpg','Assembly grounds where morning devotion and announcements are held',7],
    [8,1,'assets/images/image22.jpg','Pupils at play in the shade during lunch break',8],
    // Album 2 — Sports & Co-Curricular
    [9, 2,'assets/images/image5.jpg', 'Annual Inter-House Sports Day — athletics and cheering on the field',1],
    [10,2,'assets/images/image9.jpg', 'Under-13 boys football team training for the zonal tournament',2],
    [11,2,'assets/images/image16.jpg',"Parents' Day — outdoor activities and games for families",3],
    [12,2,'assets/images/image19.jpg','Inter-Class Cultural Gala — traditional dance from Eastern Uganda',4],
    [13,2,'assets/images/image18.jpg','Main sports field used for athletics, football and P.E. lessons',5],
    // Album 3 — Classrooms & Facilities
    [14,3,'assets/images/image6.jpg', 'Two-storey library block with dedicated reading corners for all classes',1],
    [15,3,'assets/images/image7.jpg', 'Classroom block for Primary 4 to Primary 7',2],
    [16,3,'assets/images/image8.jpg', 'Examination hall used during PLE mock and final exams',3],
    [17,3,'assets/images/image12.jpg','School chapel — daily devotion and weekly chapel services',4],
    [18,3,'assets/images/image14.jpg',"Head teacher's office and school administration block",5],
    [19,3,'assets/images/image15.jpg','Science corner — practical science lessons for P5–P7',6],
    [20,3,'assets/images/image17.jpg','Well-kept school grounds and landscaped gardens',7],
    [21,3,'assets/images/image20.jpg','Staff room — planning, meetings and marking for teachers',8],
    [22,3,'assets/images/image21.jpg','Music room — choir practice and instrument lessons',9],
    [23,3,'assets/images/image11.jpg','Primary 7 classroom focused on PLE revision',10],
    // Album 4 — School Events
    [24,4,'assets/images/image23.jpg','End of Year Christmas Party and prize-giving day',1],
    [25,4,'assets/images/image16.jpg',"Annual Parents' Day — class presentations and talent showcase",2],
];
$stmtP = $pdo->prepare('INSERT INTO gallery_photos (id, album_id, filename, caption, sort_order, uploaded_by) VALUES (?,?,?,?,?,1)');
foreach ($photos as $p) { $stmtP->execute($p); }
echo "    Inserted ".count($photos)." gallery photos.\n";

// ----------------------------------------------------------
// Step 2: News featured images  (match story!)
// ----------------------------------------------------------
echo "\n[2] Updating news.featured_image ...\n";
$newsImages = [
    1 => 'assets/images/image11.jpg', // P7 PLE Results (academics/P7 class)
    2 => 'assets/images/image5.jpg',  // Inter-House Sports Day
    3 => 'assets/images/image15.jpg', // Science Fair (science corner)
    4 => 'assets/images/image6.jpg',  // New Library Block
    5 => 'assets/images/image2.jpg',  // Martyrs Day Community Outreach (school life)
    6 => 'assets/images/image21.jpg', // Music Dance Drama Concert (music room)
    7 => 'assets/images/image9.jpg',  // Football Zonal Finals
];
$stmtN = $pdo->prepare('UPDATE news SET featured_image = ? WHERE id = ?');
foreach ($newsImages as $id => $src) {
    $stmtN->execute([$src, $id]);
    echo "    news #$id -> $src (".$stmtN->rowCount()." rows)\n";
}

// ----------------------------------------------------------
// Step 3: Events featured images
// ----------------------------------------------------------
echo "\n[3] Updating events.featured_img ...\n";
$eventImages = [
    1 => 'assets/images/image16.jpg', // Parents' Day Celebration
    2 => 'assets/images/image17.jpg', // School Open Day (tour / grounds)
    3 => 'assets/images/image8.jpg',  // End of Term II Examinations (exam hall)
    4 => 'assets/images/image13.jpg', // Career Guidance & Mentorship (assembly)
    5 => 'assets/images/image19.jpg', // Inter-Class Cultural Gala
    6 => 'assets/images/image23.jpg', // End of Year Christmas Party
];
$stmtE = $pdo->prepare('UPDATE events SET featured_img = ? WHERE id = ?');
foreach ($eventImages as $id => $src) {
    $stmtE->execute([$src, $id]);
    echo "    event #$id -> $src (".$stmtE->rowCount()." rows)\n";
}

// ----------------------------------------------------------
// Step 4: Staff photos
// ----------------------------------------------------------
echo "\n[4] Updating staff.photo ...\n";
$staffImages = [
    1 => 'assets/images/image14.jpg', // Head Teacher (admin block)
    2 => 'assets/images/image20.jpg', // Deputy Head / DoS (staff room)
    3 => 'assets/images/image12.jpg', // Chaplain (chapel)
    4 => 'assets/images/image6.jpg',  // P1 Teacher (library/younger classes)
    5 => 'assets/images/image7.jpg',  // P6 Teacher (P4–P7 block)
    6 => 'assets/images/image18.jpg', // Games/PE Teacher (sports field)
];
$stmtS = $pdo->prepare('UPDATE staff SET photo = ? WHERE id = ?');
foreach ($staffImages as $id => $src) {
    $stmtS->execute([$src, $id]);
    echo "    staff #$id -> $src (".$stmtS->rowCount()." rows)\n";
}

// ----------------------------------------------------------
// Step 5: Testimonials (optional avatars)
// ----------------------------------------------------------
echo "\n[5] Updating testimonials.photo ...\n";
$testImages = [
    1 => 'assets/images/image3.jpg',
    2 => 'assets/images/image4.jpg',
    3 => 'assets/images/image1.jpg',
    4 => 'assets/images/image22.jpg',
];
$stmtT = $pdo->prepare('UPDATE testimonials SET photo = ? WHERE id = ?');
foreach ($testImages as $id => $src) {
    $stmtT->execute([$src, $id]);
    echo "    testimonial #$id -> $src (".$stmtT->rowCount()." rows)\n";
}

// ----------------------------------------------------------
// Verification
// ----------------------------------------------------------
echo "\n=== VERIFICATION ===\n";
echo "Gallery albums  : ".$pdo->query('SELECT COUNT(*) FROM gallery_albums')->fetchColumn()."\n";
echo "Gallery photos  : ".$pdo->query('SELECT COUNT(*) FROM gallery_photos')->fetchColumn()."\n";
echo "News with img   : ".$pdo->query("SELECT COUNT(*) FROM news WHERE featured_image IS NOT NULL AND featured_image<>''")->fetchColumn()."\n";
echo "Events with img : ".$pdo->query("SELECT COUNT(*) FROM events WHERE featured_img IS NOT NULL AND featured_img<>''")->fetchColumn()."\n";
echo "Staff with photo: ".$pdo->query("SELECT COUNT(*) FROM staff WHERE photo IS NOT NULL AND photo<>''")->fetchColumn()."\n";
echo "Testimonials img: ".$pdo->query("SELECT COUNT(*) FROM testimonials WHERE photo IS NOT NULL AND photo<>''")->fetchColumn()."\n";

echo "\nAll 24 unique files from assets/images/ are referenced in the DB.\n";
echo "=== DONE ===";
echo "</pre>";
