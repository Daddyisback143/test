<?php
// php/register.php - User registration
require_once 'config.php';
require_once 'session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate all fields
    if (empty($first_name) || !preg_match('/^[A-Za-z]{2,}$/', $first_name)) {
        echo json_encode(['status' => 'error', 'msg' => 'First name should be at least 2 letters and only alphabets.']);
        exit;
    }
    if (empty($last_name) || !preg_match('/^[A-Za-z]{2,}$/', $last_name)) {
        echo json_encode(['status' => 'error', 'msg' => 'Last name should be at least 2 letters and only alphabets.']);
        exit;
    }
    if (empty($mobile) || !preg_match('/^[0-9]{10}$/', $mobile)) {
        echo json_encode(['status' => 'error', 'msg' => 'Mobile number must be 10 digits.']);
        exit;
    }
    if (empty($username) || strlen($username) < 4) {
        echo json_encode(['status' => 'error', 'msg' => 'Username should be at least 4 characters.']);
        exit;
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid email format.']);
        exit;
    }
    if (empty($password) || strlen($password) < 6) {
        echo json_encode(['status' => 'error', 'msg' => 'Password should be at least 6 characters.']);
        exit;
    }

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Username already exists']);
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Email already registered']);
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, mobile, username, email, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $first_name, $last_name, $mobile, $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'msg' => 'Registration successful! You can now login.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'msg' => 'Registration failed. Please try again.'
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method']);
}
?>
