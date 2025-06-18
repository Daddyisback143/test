<?php
// php/session_status.php - Returns session login status for AJAX check
session_start();
header('Content-Type: application/json');
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : 0;
    echo json_encode(['logged_in' => true, 'username' => $_SESSION['username'], 'is_admin' => $is_admin]);
} else {
    echo json_encode(['logged_in' => false]);
}
