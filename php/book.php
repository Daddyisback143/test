<?php
// php/book.php - Process booking request and return JSON
session_start();
require_once __DIR__ . '/../vendor/autoload.php'; // mPDF autoload
require 'config.php';

header('Content-Type: application/json');

// Function to store booking in database
function storeBooking($userId, $type, $data) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, name, age, gender, type, source, destination, date, num_travelers, fare, per_person) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $name = $data['traveler_name_1'] ?? 'Guest';
    $age = $data['traveler_age_1'] ?? 25;
    $gender = $data['traveler_gender_1'] ?? 'Other';
    $source = $data['source'] ?? '';
    $destination = $data['destination'] ?? '';
    $date = $data['date'] ?? '';
    $num_travelers = $data['num_travelers'] ?? 1;
    $fare = $data['fare'] ?? 0;
    $per_person = $data['per_person'] ?? 0;
    
    $stmt->bind_param("isissssidd", $userId, $name, $age, $gender, $type, $source, $destination, $date, $num_travelers, $fare, $per_person);
    return $stmt->execute();
}

// Helper: Generate PDF from HTML
function generatePDF($html) {
    try {
        // Create temp directory if it doesn't exist
        $tempDir = __DIR__ . '/../vendor/mpdf/mpdf/tmp';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        
        $mpdf = new \Mpdf\Mpdf([
            'tempDir' => $tempDir,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15
        ]);
        
        $mpdf->WriteHTML($html);
        return $mpdf->Output('', 'S'); // Return PDF as string
    } catch (Exception $e) {
        error_log("PDF Generation Error: " . $e->getMessage());
        return false;
    }
}

// Handle regular booking (train, bus, flight)
if (isset($_POST['type'], $_POST['date'], $_POST['num_travelers'])) {
    $type = $_POST['type'];
    $date = $_POST['date'];
    $num = intval($_POST['num_travelers']);
    $source = $_POST['source'] ?? '';
    $destination = $_POST['destination'] ?? '';
    $fare = intval($_POST['fare'] ?? 0);
    $per_person = intval($_POST['per_person'] ?? 0);
    
    $travelers = [];
    
    // Collect traveler information
    for ($i = 1; $i <= $num; $i++) {
        if (!isset($_POST["traveler_name_$i"], $_POST["traveler_age_$i"], $_POST["traveler_gender_$i"])) {
            echo json_encode(["status" => "error", "msg" => "Missing traveler info for traveler $i."]);
            exit();
        }
        
        if (empty(trim($_POST["traveler_name_$i"])) || empty(trim($_POST["traveler_age_$i"])) || empty(trim($_POST["traveler_gender_$i"])) || 
            empty(trim($_POST["traveler_mobile_$i"])) || empty(trim($_POST["traveler_email_$i"]))) {
            echo json_encode(["status" => "error", "msg" => "All traveler fields are required."]);
            exit();
        }
        
        $travelers[] = [
            'name' => $_POST["traveler_name_$i"],
            'age' => $_POST["traveler_age_$i"],
            'gender' => $_POST["traveler_gender_$i"],
            'mobile' => $_POST["traveler_mobile_$i"],
            'email' => $_POST["traveler_email_$i"]
        ];
    }
    
    // Check that source and destination are not the same
    if (strtolower(trim($source)) === strtolower(trim($destination))) {
        echo json_encode(["status" => "error", "msg" => "Source and destination cannot be the same."]);
        exit();
    }
    
    // Generate ticket information
    $ticket_no = strtoupper(bin2hex(random_bytes(4)));
    $transport_no = '';
    
    switch (strtolower($type)) {
        case 'train':
            $transport_no = 'Train No: ' . rand(40000, 49999);
            break;
        case 'flight':
            $transport_no = 'Flight No: AI' . rand(100, 9999);
            break;
        case 'bus':
            $transport_no = 'Bus No: BUS' . rand(1000, 9999);
            break;
        case 'boat':
            $transport_no = 'Boat No: B' . rand(1000, 9999);
            break;
        default:
            $transport_no = '';
    }
    
    // Store booking in database if user is logged in
    if (isset($_SESSION['user_id'])) {
        storeBooking($_SESSION['user_id'], $type, $_POST);
    }
    
    // Generate HTML ticket
    $html = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 2px solid #0077cc; border-radius: 10px; background: #f9f9f9;'>
        <h2 style='color: #0077cc; text-align: center; margin-bottom: 20px;'>TravelPlanner Ticket</h2>
        <div style='background: white; padding: 20px; border-radius: 8px;'>
            <p><strong>From:</strong> $source</p>
            <p><strong>To:</strong> $destination</p>
            <p><strong>Travel By:</strong> " . ucfirst($type) . "</p>
            <p><strong>Date:</strong> $date</p>
            <p><strong>Ticket No:</strong> $ticket_no</p>";
    
    if ($transport_no) {
        $html .= "<p><strong>$transport_no</strong></p>";
    }
    
    $html .= "<p><strong>Travelers:</strong></p><ul>";
    foreach ($travelers as $trav) {
        $html .= "<li><strong>Name:</strong> {$trav['name']} | <strong>Age:</strong> {$trav['age']} | <strong>Gender:</strong> {$trav['gender']} | <strong>Mobile:</strong> {$trav['mobile']} | <strong>Email:</strong> {$trav['email']}</li>";
    }
    $html .= "</ul>";
    
    $html .= "<p><strong>Cost per Person:</strong> ₹" . number_format($per_person) . "</p>";
    $html .= "<p><strong>Total Price:</strong> ₹" . number_format($fare) . "</p>";
    $html .= "<hr style='margin: 20px 0;'>";
    $html .= "<p style='text-align: center; color: #666;'><em>Thank you for booking with TravelPlanner!</em></p>";
    $html .= "</div></div>";
    
    $pdfData = generatePDF($html);
    
    if ($pdfData === false) {
        echo json_encode([
            "status" => "success",
            "msg" => "Booking confirmed for $num traveler(s). Total: ₹" . number_format($fare) . " (PDF generation failed, but booking is confirmed)",
            "html" => base64_encode($html),
            "pdf" => null,
            "ticket_no" => $ticket_no,
            "transport_no" => $transport_no
        ]);
    } else {
        echo json_encode([
            "status" => "success",
            "msg" => "Booking confirmed for $num traveler(s). Total: ₹" . number_format($fare),
            "html" => base64_encode($html),
            "pdf" => base64_encode($pdfData),
            "ticket_no" => $ticket_no,
            "transport_no" => $transport_no
        ]);
    }
    exit();
}

// Handle package booking
if (isset($_POST['package_name'], $_POST['travel_date'], $_POST['num_persons'])) {
    $packageName = $_POST['package_name'];
    $travelDate = $_POST['travel_date'];
    $numPersons = intval($_POST['num_persons']);
    $phone = $_POST['phone'] ?? '';
    $ages = $_POST['age'] ?? [];
    $genders = $_POST['gender'] ?? [];
    
    // Package data (you can move this to database)
    $packages = [
        "Goa Beach Getaway" => ["price" => 18999, "days" => 5, "nights" => 4],
        "Himachal Adventure Escape" => ["price" => 21999, "days" => 7, "nights" => 6],
        "Kerala Backwaters Retreat" => ["price" => 20499, "days" => 6, "nights" => 5],
        "Maldives Explorer" => ["price" => 24999, "days" => 4, "nights" => 3],
        "Thailand Delight" => ["price" => 28999, "days" => 5, "nights" => 4],
        "Dubai Luxury Tour" => ["price" => 37999, "days" => 4, "nights" => 3]
    ];
    
    if (!isset($packages[$packageName])) {
        echo json_encode(["status" => "error", "msg" => "Package not found."]);
        exit();
    }
    
    $package = $packages[$packageName];
    $totalPrice = $package['price'] * $numPersons;
    
    // Generate booking reference
    $booking_ref = 'PKG' . strtoupper(bin2hex(random_bytes(4)));
    
    // Generate HTML confirmation
    $html = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 2px solid #0077cc; border-radius: 10px; background: #f9f9f9;'>
        <h2 style='color: #0077cc; text-align: center; margin-bottom: 20px;'>Package Booking Confirmation</h2>
        <div style='background: white; padding: 20px; border-radius: 8px;'>
            <p><strong>Package:</strong> $packageName</p>
            <p><strong>Duration:</strong> {$package['days']} Days / {$package['nights']} Nights</p>
            <p><strong>Travel Date:</strong> $travelDate</p>
            <p><strong>Number of Persons:</strong> $numPersons</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Booking Reference:</strong> $booking_ref</p>
            <p><strong>Price per Person:</strong> ₹" . number_format($package['price']) . "</p>
            <p><strong>Total Price:</strong> ₹" . number_format($totalPrice) . "</p>
            <hr style='margin: 20px 0;'>
            <p style='text-align: center; color: #666;'><em>Thank you for booking with TravelPlanner!</em></p>
        </div>
    </div>";
    
    $pdfData = generatePDF($html);
    echo json_encode([
        "status" => "success",
        "msg" => "Package booking confirmed for $numPersons person(s). Total: ₹" . number_format($totalPrice),
        "html" => base64_encode($html),
        "pdf" => base64_encode($pdfData),
        "booking_ref" => $booking_ref
    ]);
    exit();
}

// If no valid booking data
echo json_encode(["status" => "error", "msg" => "Invalid booking data."]);
?>
