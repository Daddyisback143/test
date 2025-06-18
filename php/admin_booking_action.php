<?php
// php/admin_booking_action.php - Admin booking management (view ticket)
session_start();
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    die('Access denied.');
}
// For demo: expects booking_index and finds booking in all user files
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_index'])) {
    $index = intval($_POST['booking_index']);
    $dir = __DIR__ . '/../user_bookings/';
    $allBookings = [];
    foreach (glob($dir . 'user_*.json') as $file) {
        $userId = preg_replace('/\D+/', '', basename($file));
        $bookings = json_decode(file_get_contents($file), true);
        if (is_array($bookings)) {
            foreach ($bookings as $b) {
                $b['user_id'] = $userId;
                $allBookings[] = $b;
            }
        }
    }
    if (isset($allBookings[$index])) {
        // Show booking details (or generate ticket)
        $b = $allBookings[$index];
        echo '<h2>Booking Details</h2>';
        echo '<pre>' . htmlspecialchars(print_r($b, true)) . '</pre>';
        echo '<form method="post" action="download_ticket.php"><input type="hidden" name="booking_data" value="'.htmlspecialchars(json_encode($b)).'">';
        echo '<button type="submit">Download Ticket PDF</button></form>';
        echo '<a href="../admin_dashboard.php">Back to dashboard</a>';
        exit();
    } else {
        echo 'Booking not found.';
        exit();
    }
}
echo 'Invalid request.';
