<?php
// actions/login_customer_action.php
header('Content-Type: application/json');
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../controllers/customer_controller.php';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'You are already logged in.']);
    exit;
}

// Collect inputs
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

// Server-side validation
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required.';
}

if (empty($password)) {
    $errors[] = 'Password is required.';
}

if ($errors) {
    echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
    exit;
}

// Attempt login
$login_result = login_customer_ctr([
    'email' => $email,
    'password' => $password
]);

if ($login_result['status'] === 'success') {
    // Set session variables
    $_SESSION['user_id'] = $login_result['user_id'];
    $_SESSION['user_role'] = $login_result['user_role'];
    $_SESSION['user_name'] = $login_result['user_name'];
    $_SESSION['user_email'] = $login_result['user_email'];
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Login successful!',
        'user_role' => $login_result['user_role'],
        'redirect' => '../index.php'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => $login_result['message']
    ]);
}
?>
