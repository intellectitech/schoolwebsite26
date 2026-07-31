<?php
// actions/add_event.php

include '../db_connect.php';
require_once __DIR__ . '/../auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitize & retrieve POST parameters
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date  = trim($_POST['event_date'] ?? '');
    $location    = trim($_POST['location'] ?? '');

    // 2. Simple Validation
    if (!empty($title) && !empty($event_date)) {
        try {
            // 3. Insert record into database
            $stmt = $pdo->prepare(
                'INSERT INTO events (title, description, event_date, location) 
                 VALUES (:title, :description, :event_date, :location)'
            );
            
            $stmt->execute([
                ':title'       => $title,
                ':description' => $description,
                ':event_date'  => $event_date,
                ':location'    => $location
            ]);

            // Optional: Log action to audit log
            if (isset($_SESSION['admin_id'])) {
                $audit = $pdo->prepare(
                    'INSERT INTO audit_log (admin_id, action) VALUES (:admin_id, :action)'
                );
                $audit->execute([
                    ':admin_id' => $_SESSION['admin_id'],
                    ':action'   => 'Created event: ' . $title
                ]);
            }

            // Redirect back to events section with success message
            header('Location: ../dashboard.php#events?success=' . urlencode('Event created successfully.'));
            exit();

        } catch (PDOException $e) {
            // Redirect back with database error message
            header('Location: ../dashboard.php#events?error=' . urlencode('Database error: ' . $e->getMessage()));
            exit();
        }
    } else {
        header('Location: ../dashboard.php#events?error=' . urlencode('Title and Event Date are required fields.'));
        exit();
    }
} else {
    header('Location: ../dashboard.php#events');
    exit();
}