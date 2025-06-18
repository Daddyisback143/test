<?php
// download_ticket.php - Generate PDF for a booking from My Bookings
require_once __DIR__ . '/../vendor/autoload.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<p>Login required.</p>";
    exit();
}
if (!isset($_POST['booking']) && !isset($_POST['booking_data'])) {
    echo "<p>Invalid request.</p>";
    exit();
}

// If booking_data is posted (from admin), use it directly
if (isset($_POST['booking_data'])) {
    $booking = json_decode($_POST['booking_data'], true);
    if (!$booking) die('Invalid booking data.');
} else {
    $booking = json_decode($_POST['booking'], true);
}

$type = $booking['type'] ?? '';
$mpdf = new \Mpdf\Mpdf();
$html = "<html><head><meta charset='UTF-8'><style>body{font-family:'Poppins',Arial,sans-serif;background:#f9f9f9;color:#222;}h2{color:#0077cc;}b{color:#333;}ul{padding-left:1.2em;}li{margin-bottom:0.5em;}div.ticket-box{background:#fff;border-radius:10px;box-shadow:0 2px 8px #0001;padding:2em;max-width:600px;margin:2em auto;}hr{margin:1.5em 0;}</style></head><body><div class='ticket-box'>";
if ($type === 'destination') {
    $html .= "<h2>TravelPlanner Destination Booking</h2>";
    $html .= "<b>Destination:</b> " . htmlspecialchars($booking['destination_name']) . "<br>";
    $html .= "<b>Travel Date:</b> " . htmlspecialchars($booking['travel_date']) . "<br>";
    $html .= "<b>Phone:</b> " . htmlspecialchars($booking['phone']) . "<br>";
    $html .= "<b>Travelers:</b><ul>";
    foreach ($booking['traveler_name'] as $i => $name) {
        $age = $booking['age'][$i] ?? 'N/A';
        $gender = $booking['gender'][$i] ?? 'N/A';
        $html .= "<li>Traveler " . ($i+1) . ": Name: " . htmlspecialchars($name) . ", Age: " . htmlspecialchars($age) . ", Gender: " . htmlspecialchars($gender) . "</li>";
    }
    $html .= "</ul>";
    $html .= "<hr><i>Thank you for booking with TravelPlanner!</i>";
} else if (
    isset(
        $booking['type'], $booking['date'], $booking['num_travelers'], $booking['source'], $booking['destination'], $booking['fare'], $booking['per_person'], $booking['travelers']
    )
) {
    $type = $booking['type'];
    $date = $booking['date'];
    $num = $booking['num_travelers'];
    $source = $booking['source'];
    $destination = $booking['destination'];
    $fare = $booking['fare'];
    $per_person = $booking['per_person'];
    $travelers = $booking['travelers'];
    $ticket_no = $booking['ticket_no'] ?? '';
    $transport_no = $booking['transport_no'] ?? '';
    $html .= "<h2>TravelPlanner Ticket</h2>";
    $html .= "<b>From:</b> " . htmlspecialchars($source) . "<br>";
    $html .= "<b>To:</b> " . htmlspecialchars($destination) . "<br>";
    $html .= "<b>Travel By:</b> " . ucfirst($type) . "<br>";
    $html .= "<b>Date:</b> " . htmlspecialchars($date) . "<br>";
    $html .= "<b>Travelers:</b><ul>";
    foreach ($travelers as $t) {
        $html .= "<li>Name: " . htmlspecialchars($t['name']) . " | Age: " . htmlspecialchars($t['age']) . " | Gender: " . htmlspecialchars($t['gender']) . "</li>";
    }
    $html .= "</ul>";
    $html .= "<b>Cost per Person:</b> ₹" . number_format($per_person) . "<br>";
    $html .= "<b>Total Price:</b> ₹" . number_format($fare) . "<br>";
    $html .= "<b>Ticket No:</b> $ticket_no<br>";
    if ($transport_no) $html .= "<b>$transport_no</b><br>";
    $html .= "<hr><i>Thank you for booking with TravelPlanner!</i>";
}
$html .= "</div></body></html>";
$mpdf->WriteHTML($html);
$mpdf->Output('TravelPlanner_Booking.pdf', 'D');
exit();
