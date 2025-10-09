<?php
// actions/login_customer_action.php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/customer_controller.php';

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
