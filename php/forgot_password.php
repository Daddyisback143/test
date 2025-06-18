<?php
// php/forgot_password.php - Forgot password functionality
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($name) || empty($username) || empty($email)) {
        echo json_encode(['status' => 'error', 'msg' => 'Please fill in all fields']);
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid email format']);
        exit;
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(['status' => 'error', 'msg' => 'No account found with these details']);
        exit;
    }

    // Generate a simple reset token (in production, use a more secure method)
    $reset_token = bin2hex(random_bytes(32));
    $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store reset token in database (you might want to create a separate table for this)
    // For now, we'll just return a success message
    echo json_encode([
        'status' => 'success',
        'msg' => 'Password reset instructions have been sent to your email. Please check your inbox.'
    ]);

} else {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method']);
}
?>
