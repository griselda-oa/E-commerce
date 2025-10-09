<?php
// actions/login_customer_action.php
header('Content-Type: application/json');

// Disable error display to prevent HTML in JSON response
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Check if required files exist before including
$core_path = __DIR__ . '/../settings/core.php';
$controller_path = __DIR__ . '/../controllers/customer_controller.php';

if (!file_exists($core_path) || !file_exists($controller_path)) {
    echo json_encode(['status' => 'error', 'message' => 'Required files missing. Please check file upload.']);
    exit;
}

require_once $core_path;
require_once $controller_path;

// Note: Already logged in check is handled in login.php

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
    // Set session variables using core.php function
    set_user_session($login_result);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Login successful!',
        'user_role' => $login_result['user_role'],
        'is_admin' => $login_result['is_admin'] ?? 0,
        'redirect' => '../index.php'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => $login_result['message']
    ]);
}
?>
