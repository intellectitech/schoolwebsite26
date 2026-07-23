<?php
//Cached lookup from school_info table - only queries DB once per key page load
function getSettings($pdo, $key){
    static $cache = [];
    if (isset($cache[$key])){
        return $cache[$key];
    }
    $stmt = $pdo->prepare("SELECT setting_value FROM school_info WHERE setting_key = ?");
    $stmt->execute([$key]);
    $cache[$key] = $stmt->fetchColumn() ?:'';
    return $cache[$key];
}

//Truncate text for news card excerpts
function excerpt($text, $len = 120){
    $text = strip_tags($text);
    return strlen($text) > $len ? substr($text, 0, $len) . '...' : $text;
}