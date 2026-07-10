<?php
require_once 'config/database.php';
echo "Database connection established successfully.";
if ($pdo) {
    echo "Connection to the database was successful.";
} else {
    echo "Failed to connect to the database.";
}
?>