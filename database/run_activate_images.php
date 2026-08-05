<?php
require_once __DIR__ . '/../includes/functions.php';

$sql = file_get_contents(__DIR__ . '/activate_images.sql');

echo "<pre>Applying activate_images.sql ...\n";

try {
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    $count = 0;
    foreach ($statements as $stmt) {
        if ($stmt === '' || strpos($stmt, '--') === 0 || strpos($stmt, '/*') === 0) continue;
        // Skip USE statement since we're already on the right DB via PDO
        if (preg_match('/^USE\s+/i', $stmt)) continue;
        try {
            $affected = $pdo->exec($stmt);
            if ($affected === false) {
                echo "[FAIL] " . substr($stmt, 0, 80) . " ...\n";
                $err = $pdo->errorInfo();
                echo "      Error: " . $err[2] . "\n";
            } else {
                echo "[OK]   " . substr($stmt, 0, 70) . str_repeat(' ', max(0,70-strlen(substr($stmt,0,70)))) . " ({$affected} rows)\n";
                $count++;
            }
        } catch (PDOException $e) {
            echo "[ERR]  " . substr($stmt, 0, 80) . " ...\n";
            echo "      " . $e->getMessage() . "\n";
        }
    }
    echo "\nDone. {$count} statements ran successfully.\n\n";

    echo "--- Quick verification ---\n";
    echo "Gallery albums: " . $pdo->query('SELECT COUNT(*) FROM gallery_albums')->fetchColumn() . "\n";
    echo "Gallery photos: " . $pdo->query('SELECT COUNT(*) FROM gallery_photos')->fetchColumn() . "\n";
    echo "News with images: " . $pdo->query("SELECT COUNT(*) FROM news WHERE featured_image IS NOT NULL AND featured_image <> ''")->fetchColumn() . "\n";
    echo "Events with images: " . $pdo->query("SELECT COUNT(*) FROM events WHERE featured_img IS NOT NULL AND featured_img <> ''")->fetchColumn() . "\n";
    echo "Staff with photos: " . $pdo->query("SELECT COUNT(*) FROM staff WHERE photo IS NOT NULL AND photo <> ''")->fetchColumn() . "\n";

    echo "\nSample gallery photos:\n";
    foreach ($pdo->query('SELECT p.id, a.name AS album, p.filename, p.caption FROM gallery_photos p LEFT JOIN gallery_albums a ON a.id=p.album_id ORDER BY p.album_id, p.sort_order LIMIT 6') as $r) {
        echo "  [{$r['id']}] {$r['album']} : {$r['filename']}  ->  {$r['caption']}\n";
    }
} catch (Throwable $e) {
    echo "FATAL: " . $e->getMessage() . "\n";
}
echo "</pre>";
