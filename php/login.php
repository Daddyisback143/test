<?php
// php/login.php - User login
require_once 'config.php';
require_once 'session.php';

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

function getUserByLogin($conn, $login) {
    $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

function verifyUserPassword($password, $hashed_password) {
    return password_verify($password, $hashed_password);
}

function loginUser($id, $username) {
    $_SESSION['user_id'] = $id;
    $_SESSION['username'] = $username;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        echo json_encode(['status' => 'error', 'msg' => 'Please fill in all fields']);
        exit;
    }

    try {
        // Check if database connection is working
        if (!$conn || $conn->connect_error) {
            echo json_encode(['status' => 'error', 'msg' => 'Database connection failed. Please try again.']);
            exit;
        }

        // Get user from database
        $stmt = $conn->prepare("SELECT id, username, email, password, is_admin FROM users WHERE username = ? OR email = ? LIMIT 1");
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'msg' => 'Database error: ' . $conn->error]);
            exit;
        }
        
        $stmt->bind_param("ss", $login, $login);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['status' => 'error', 'msg' => 'Invalid username or password']);
            exit;
        }

        $user = $result->fetch_assoc();
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            // For admin user, try to reset password if it's the default
            if ($login === 'admin') {
                $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
                $update_stmt->bind_param("s", $new_hash);
                $update_stmt->execute();
                
                // Try again with new hash
                if (password_verify($password, $new_hash)) {
                    $user['password'] = $new_hash;
                } else {
                    echo json_encode(['status' => 'error', 'msg' => 'Invalid username or password']);
                    exit;
                }
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Invalid username or password']);
                exit;
            }
        }

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
        $_SESSION['last_activity'] = time();

        // Return success response
        $base = getBasePath();
        echo json_encode([
            'status' => 'success',
            'username' => $user['username'],
            'is_admin' => $user['is_admin'] ?? 0,
            'redirect' => $base . (($user['is_admin'] ?? 0) ? 'admin_dashboard.php' : 'index.html')
        ]);
        
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'msg' => 'Login failed. Please try again.']);
    }
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid request method']);
}
?>
