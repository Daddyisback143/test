<?php
// admin_dashboard.php - Admin dashboard for TravelPlanner
session_start();
require_once __DIR__ . '/php/config.php';
require_once __DIR__ . '/php/session.php';
// Anti-cache headers
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
// Only allow access if logged in as admin
requireAdmin();

function getAllBookings($userBookingsDir) {
    $allBookings = [];
    $totalRevenue = 0;
    if (is_dir($userBookingsDir)) {
        foreach (glob($userBookingsDir . 'user_*.json') as $file) {
            $userId = preg_replace('/\D+/', '', basename($file));
            $bookings = json_decode(file_get_contents($file), true);
            if (is_array($bookings)) {
                foreach ($bookings as $b) {
                    $b['user_id'] = $userId;
                    $allBookings[] = $b;
                    if (isset($b['total_price'])) $totalRevenue += (int)$b['total_price'];
                }
            }
        }
    }
    return [$allBookings, $totalRevenue];
}

function getAllUsers($conn) {
    $users = [];
    $res = $conn->query('SELECT id, username, email, created_at FROM users');
    while ($row = $res->fetch_assoc()) {
        $users[] = $row;
    }
    return $users;
}

function getAllContacts($conn) {
    $contacts = [];
    if ($conn->query("SHOW TABLES LIKE 'contact_messages'")->num_rows) {
        $res = $conn->query('SELECT name, email, message, created_at FROM contact_messages');
        while ($row = $res->fetch_assoc()) {
            $contacts[] = $row;
        }
    }
    return $contacts;
}

// Export CSV if requested
if (isset($_GET['export']) && $_GET['export'] === 'bookings') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="all_bookings.csv"');
    [$allBookings, $totalRevenue] = getAllBookings(__DIR__ . '/user_bookings/');
    $out = fopen('php://output', 'w');
    if (!empty($allBookings)) {
        // Write header
        $header = array_keys($allBookings[0]);
        fputcsv($out, $header);
        // Write each row, ensuring all columns are present and in order
        foreach ($allBookings as $b) {
            $row = [];
            foreach ($header as $col) {
                $row[] = isset($b[$col]) ? (is_array($b[$col]) ? json_encode($b[$col]) : $b[$col]) : '';
            }
            fputcsv($out, $row);
        }
    }
    fclose($out);
    exit();
}
if (isset($_GET['export']) && $_GET['export'] === 'users') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="all_users.csv"');
    $users = getAllUsers($conn);
    $out = fopen('php://output', 'w');
    if (!empty($users)) {
        $header = array_keys($users[0]);
        fputcsv($out, $header);
        foreach ($users as $u) {
            $row = [];
            foreach ($header as $col) {
                $row[] = isset($u[$col]) ? $u[$col] : '';
            }
            fputcsv($out, $row);
        }
    }
    fclose($out);
    exit();
}
if (isset($_GET['export']) && $_GET['export'] === 'contacts') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="all_contacts.csv"');
    $contacts = getAllContacts($conn);
    $out = fopen('php://output', 'w');
    if (!empty($contacts)) {
        $header = array_keys($contacts[0]);
        fputcsv($out, $header);
        foreach ($contacts as $c) {
            $row = [];
            foreach ($header as $col) {
                $row[] = isset($c[$col]) ? $c[$col] : '';
            }
            fputcsv($out, $row);
        }
    }
    fclose($out);
    exit();
}

[$allBookings, $totalRevenue] = getAllBookings(__DIR__ . '/user_bookings/');
$users = getAllUsers($conn);
$contacts = getAllContacts($conn);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard - TravelPlanner</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: linear-gradient(120deg, #f0f4f9 0%, #e0eafc 100%); font-family: 'Poppins', Arial, sans-serif; }
        .container { max-width: 1200px; margin: 2em auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px #0077cc22; padding: 2.5em 2em; }
        h1 { color: #0077cc; font-size: 2.5em; margin-bottom: 0.5em; }
        h2 { color: #2193b0; margin-top: 2em; }
        ul.summary-list { list-style: none; padding: 0; margin: 0 0 2em 0; display: flex; flex-wrap: wrap; gap: 2em; }
        ul.summary-list li { background: linear-gradient(90deg, #6dd5ed 0%, #2193b0 100%); color: #fff; border-radius: 10px; padding: 1.2em 2em; font-size: 1.2em; min-width: 220px; box-shadow: 0 2px 8px #2193b033; }
        .admin-nav { margin-bottom: 2em; display: flex; gap: 1.5em; }
        .admin-nav a { color: #fff; background: #0077cc; padding: 0.7em 1.5em; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background 0.2s; }
        .admin-nav a:hover { background: #2193b0; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 2em; background: #f9f9f9; border-radius: 10px; overflow: hidden; }
        th, td { border: 1px solid #e0eafc; padding: 0.7em; text-align: left; }
        th { background: #0077cc; color: #fff; font-weight: 600; }
        tr:nth-child(even) { background: #f0f4f9; }
        .export-btn { background: #28a745; color: #fff; border: none; padding: 0.5em 1.2em; border-radius: 5px; cursor: pointer; margin-bottom: 1em; font-weight: 600; }
        .export-btn:hover { background: #218838; }
        .logout-btn { background: #e74c3c; color: #fff; border: none; padding: 0.5em 1.2em; border-radius: 5px; cursor: pointer; font-weight: 600; margin-left: auto; }
        .logout-btn:hover { background: #c0392b; }
        @media (max-width: 900px) { .container { padding: 1em; } ul.summary-list { flex-direction: column; gap: 1em; } }
    </style>
</head>
<body>
<div class="container">
    <div style="text-align:right; margin-bottom:1em; font-size:1.1em; color:#0077cc; font-weight:600;">Welcome, admin</div>
    <div class="admin-nav">
        <a href="admin_dashboard.php">Dashboard Home</a>
        <a href="?export=bookings">Download All Bookings (CSV)</a>
        <a href="?export=users">Download All Users (CSV)</a>
        <a href="?export=contacts">Download All Contacts (CSV)</a>
        <form action="php/logout.php" method="post" style="display:inline;"><button class="logout-btn" type="submit">Logout</button></form>
    </div>
    <h1>Admin Dashboard</h1>
    <ul class="summary-list">
        <li><b>Total Registered Users:</b><br> <?= count($users) ?></li>
        <li><b>Total Bookings:</b><br> <?= count($allBookings) ?></li>
        <li><b>Total Revenue:</b><br> ₹<?= number_format($totalRevenue) ?></li>
        <li><b>Total Contact Messages:</b><br> <?= count($contacts) ?></li>
    </ul>
    <h2>User Management</h2>
    <div style="overflow-x:auto; margin-bottom:2em;">
    <table>
        <tr>
            <?php if (!empty($users)) foreach (array_keys($users[0]) as $col) echo "<th>".htmlspecialchars($col)."</th>"; ?>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $u): ?>
        <tr>
            <?php foreach ($u as $k=>$v) echo "<td>".htmlspecialchars($v)."</td>"; ?>
            <td>
                <form action="php/admin_user_action.php" method="post" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($u['id']) ?>">
                    <button type="submit" name="action" value="edit" class="export-btn" style="background:#0077cc;">Edit</button>
                    <button type="submit" name="action" value="resetpw" class="export-btn" style="background:#e67e22;">Reset Password</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <h2>All Bookings <a href="?export=bookings" class="export-btn">Download CSV</a></h2>
    <div style="overflow-x:auto;">
    <table>
        <tr>
            <?php if (!empty($allBookings)) foreach (array_keys($allBookings[0]) as $col) echo "<th>".htmlspecialchars($col)."</th>"; ?>
            <th>Actions</th>
        </tr>
        <?php foreach ($allBookings as $i=>$b): ?>
        <tr>
            <?php foreach ($b as $v) echo "<td>".htmlspecialchars(is_array($v) ? json_encode($v) : $v)."</td>"; ?>
            <td>
                <form action="php/admin_booking_action.php" method="post" style="display:inline;">
                    <input type="hidden" name="booking_index" value="<?= $i ?>">
                    <button type="submit" name="action" value="view" class="export-btn" style="background:#0077cc;">View Ticket</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <h2>All Contact Messages <a href="?export=contacts" class="export-btn">Download CSV</a></h2>
    <div style="overflow-x:auto;">
    <table>
        <tr>
            <?php if (!empty($contacts)) foreach (array_keys($contacts[0]) as $col) echo "<th>".htmlspecialchars($col)."</th>"; ?>
        </tr>
        <?php foreach ($contacts as $c): ?>
        <tr>
            <?php foreach ($c as $v) echo "<td>".htmlspecialchars($v)."</td>"; ?>
        </tr>
        <?php endforeach; ?>
    </table>
    </div>
    <h2>Password Reset Requests</h2>
    <div style="overflow-x:auto; margin-bottom:2em;">
    <?php
    $resetDir = __DIR__ . '/reset_requests/';
    $resetFiles = is_dir($resetDir) ? glob($resetDir.'reset_*.json') : [];
    if ($resetFiles): ?>
    <table>
        <tr><th>Name</th><th>Username</th><th>Email</th><th>Requested At</th><th>Action</th></tr>
        <?php foreach ($resetFiles as $file): $req = json_decode(file_get_contents($file), true); ?>
        <tr>
            <td><?= htmlspecialchars($req['name']) ?></td>
            <td><?= htmlspecialchars($req['username']) ?></td>
            <td><?= htmlspecialchars($req['email']) ?></td>
            <td><?= htmlspecialchars($req['timestamp']) ?></td>
            <td>
                <form action="php/admin_user_action.php" method="post" style="display:inline;">
                    <input type="hidden" name="reset_file" value="<?= htmlspecialchars(basename($file)) ?>">
                    <input type="hidden" name="username" value="<?= htmlspecialchars($req['username']) ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($req['email']) ?>">
                    <button type="submit" name="action" value="grant_reset" class="export-btn" style="background:#e67e22;">Grant & Send Temp Password</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <div style="color:#888;">No pending reset requests.</div>
    <?php endif; ?>
    </div>
</div>
</body>
</html>
