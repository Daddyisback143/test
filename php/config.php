<?php
// php/config.php - Database connection settings

// Set session cookie params BEFORE starting session
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    $secure = false; // Set to true if using HTTPS
    $httponly = true;
    $samesite = 'Lax'; // Or 'None' if using cross-site cookies
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite
        ]);
    } else {
        session_set_cookie_params(0, '/; samesite=' . $samesite, '', $secure, $httponly);
    }
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "travelplanner";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Ensure admin user always exists with username 'admin' and password 'admin123'
$admin_username = 'admin';
$admin_password = 'admin123';
$admin_hash = password_hash($admin_password, PASSWORD_DEFAULT);
$check_admin = $conn->query("SELECT id FROM users WHERE username='admin'");
if ($check_admin && $check_admin->num_rows === 0) {
    $conn->query("INSERT INTO users (username, password, email, created_at) VALUES ('admin', '$admin_hash', 'admin@travelplanner.local', NOW())");
} else if ($check_admin && $check_admin->num_rows > 0) {
    // Always update admin password to admin123 for safety
    $conn->query("UPDATE users SET password='$admin_hash' WHERE username='admin'");
}
?>
