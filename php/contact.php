<?php
// php/contact.php: Handle contact form submissions
require 'config.php';

header('Content-Type: application/json');
// Suppress PHP errors from being output as HTML
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $message = $_POST['message'] ?? '';

        if (empty($name) || empty($email) || empty($message)) {
            echo json_encode(['status' => 'error', 'msg' => 'Please fill in all fields']);
            exit;
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'msg' => 'Invalid email format']);
            exit;
        }

        // Check if contact_messages table exists
        $tableCheck = $conn->query("SHOW TABLES LIKE 'contact_messages'");
        if ($tableCheck->num_rows === 0) {
            echo json_encode(['status' => 'error', 'msg' => 'Contact form is not set up. Please contact admin.']);
            exit;
        }

        // Insert message into database
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'msg' => 'Database error: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            // Send email notification to admin (optional, suppress errors)
            $to = "admin@travelplanner.com";
            $subject = "New Contact Form Submission";
            $email_message = "Name: $name\n";
            $email_message .= "Email: $email\n\n";
            $email_message .= "Message:\n$message";
            $headers = "From: $email";
            @mail($to, $subject, $email_message, $headers);

            echo json_encode([
                'status' => 'success',
                'msg' => 'Thank you for your message! We will get back to you soon.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'msg' => 'Failed to send message. Please try again.'
            ]);
        }
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid request method']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Server error: ' . $e->getMessage()]);
}
?>
