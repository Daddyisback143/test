<?php
// mybookings.php - Show all bookings for the logged-in user
session_start();
require_once __DIR__ . '/session.php';
// Anti-cache headers
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
requireLogin();
$userId = $_SESSION['user_id'];
$dir = __DIR__ . '/../user_bookings/';
$file = $dir . 'user_' . $userId . '.json';
$bookings = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - TravelPlanner</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .booking-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 8px #0001; padding: 1.5em; margin: 1em 0; }
        .booking-card h3 { color: #0077cc; }
        .download-btn { background: #0077cc; color: #fff; border: none; padding: 0.5em 1em; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>My Bookings</h1>
    <?php if (!$bookings): ?>
        <p>No bookings found.</p>
    <?php else: ?>
        <?php foreach (array_reverse($bookings) as $b): ?>
            <div class="booking-card">
                <h3>
                    <?php
                    // Show package name or destination
                    if (isset($b['package_name'])) {
                        echo htmlspecialchars($b['package_name']);
                    } elseif (isset($b['destination'])) {
                        echo htmlspecialchars($b['destination']);
                    } elseif (isset($b['destination_name'])) {
                        echo htmlspecialchars($b['destination_name']);
                    } else {
                        echo 'Booking';
                    }
                    ?>
                </h3>
                <div><b>Type:</b> <?= htmlspecialchars($b['type'] ?? 'N/A') ?></div>
                <div><b>Date:</b> <?= htmlspecialchars($b['travel_date'] ?? ($b['date'] ?? '')) ?></div>
                <div><b>Persons:</b> <?= htmlspecialchars($b['num_persons'] ?? ($b['num_travelers'] ?? '')) ?></div>
                <div><b>Phone:</b> <?= htmlspecialchars($b['phone'] ?? 'N/A') ?></div>
                <div><b>Booked At:</b> <?= htmlspecialchars($b['timestamp'] ?? '') ?></div>
                <form method="post" action="download_ticket.php" target="_blank">
                    <input type="hidden" name="booking" value='<?= htmlspecialchars(json_encode($b), ENT_QUOTES) ?>'>
                    <button class="download-btn">Download Ticket (PDF)</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <a href="../index.html" class="back-btn" style="display:inline-block;margin-top:20px;padding:10px 20px;background:#0077cc;color:#fff;text-decoration:none;border-radius:5px;">&larr; Back to Home</a>
</body>
</html>
