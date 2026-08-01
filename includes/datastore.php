<?php
/**
 * includes/datastore.php
 * ---------------------------------------------------------------
 * A tiny file-based data layer. No MySQL, no database server —
 * everything the admin dashboard manages (news, gallery, events,
 * staff, admissions inquiries, contact messages, admin login) is
 * stored as plain PHP array files inside the /data folder.
 *
 * Why .php files instead of .json? A .json file, if ever requested
 * directly in a browser, would display its raw contents (including
 * things like the admin password hash or parents' phone numbers).
 * A .php data file is instead *executed* by the server — visiting
 * it directly just returns a blank page, on any host, with zero
 * extra configuration. This keeps the whole site deployable on
 * ordinary shared PHP hosting with no database setup at all.
 * ---------------------------------------------------------------
 */

define('DATA_DIR', __DIR__ . '/../data');

/**
 * Reads a data file and returns it as an array.
 * Returns an empty array if the file doesn't exist or is invalid.
 */
function ds_read($name) {
    $path = DATA_DIR . '/' . $name . '.php';
    if (!file_exists($path)) {
        return [];
    }
    $data = include $path;
    return is_array($data) ? $data : [];
}

/**
 * Writes an array back to a data file (locked against concurrent writes).
 */
function ds_write($name, array $data) {
    if (!is_dir(DATA_DIR)) {
        mkdir(DATA_DIR, 0755, true);
    }
    $path = DATA_DIR . '/' . $name . '.php';
    $tmpPath = $path . '.tmp';

    $php = "<?php\n// Auto-generated data file — edit only through the admin dashboard.\nreturn " . var_export($data, true) . ";\n";

    $fp = fopen($tmpPath, 'w');
    if (!$fp) { return false; }
    flock($fp, LOCK_EX);
    fwrite($fp, $php);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    return rename($tmpPath, $path);
}

/** Reads a single-object data file (e.g. the admin account record). */
function ds_read_object($name) {
    $path = DATA_DIR . '/' . $name . '.php';
    if (!file_exists($path)) { return null; }
    $data = include $path;
    return is_array($data) ? $data : null;
}

/** Writes a single-object data file. */
function ds_write_object($name, array $data) {
    return ds_write($name, $data);
}

/** Returns the next auto-increment style id for a list of rows. */
function ds_next_id(array $rows) {
    $max = 0;
    foreach ($rows as $r) {
        if (isset($r['id']) && (int) $r['id'] > $max) { $max = (int) $r['id']; }
    }
    return $max + 1;
}

/** Finds a row by id. Returns null if not found. */
function ds_find(array $rows, $id) {
    foreach ($rows as $r) {
        if ((string) ($r['id'] ?? '') === (string) $id) { return $r; }
    }
    return null;
}

/** Removes a row by id. Returns the filtered array. */
function ds_remove(array $rows, $id) {
    return array_values(array_filter($rows, function ($r) use ($id) {
        return (string) ($r['id'] ?? '') !== (string) $id;
    }));
}

/** Replaces a row by id with new data (keeps original array order). */
function ds_update(array $rows, $id, array $newData) {
    foreach ($rows as $i => $r) {
        if ((string) ($r['id'] ?? '') === (string) $id) {
            $rows[$i] = array_merge($r, $newData);
            break;
        }
    }
    return $rows;
}

/** Turns a title into a URL-friendly slug. */
function make_slug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Truncates a string to a max length, adding a trailing marker if cut.
 * A dependency-free stand-in for mb_strimwidth() (which needs the
 * mbstring extension — not guaranteed on all budget hosting).
 */
function truncate_text($text, $maxLength = 100, $marker = '...') {
    $text = (string) $text;
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($text) <= $maxLength) { return $text; }
        return mb_substr($text, 0, $maxLength) . $marker;
    }
    if (strlen($text) <= $maxLength) { return $text; }
    return substr($text, 0, $maxLength) . $marker;
}
