<?php

header('Content-Type: application/json');

session_start();

$response = array();

// TODO: Check if the user is already logged in and redirect to the dashboard
if (isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/user_controller.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$phone_number = $_POST['phone_number'];
// Enforce default role on server: 2 = regular user
$role = 2;

// Basic backend validation (defense in depth)
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email address';
    echo json_encode($response);
    exit();
}

if (strlen($password) < 8 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $response['status'] = 'error';
    $response['message'] = 'Weak password. Use 8+ chars with upper, lower and number.';
    echo json_encode($response);
    exit();
}

// Basic phone validation (E.164 style)
$phone_clean = preg_replace('/\s|-/', '', $phone_number);
if (!preg_match('/^\+?[1-9]\d{7,14}$/', $phone_clean)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid phone number format.';
    echo json_encode($response);
    exit();
}

// Whitelist allowed countries if provided
$allowed_countries = ['Ghana','Nigeria','Kenya','South Africa','United Kingdom','United States','Canada'];
$country = $_POST['country'] ?? '';
if (!in_array($country, $allowed_countries, true)) {
    $response['status'] = 'error';
    $response['message'] = 'Select a valid country.';
    echo json_encode($response);
    exit();
}

$user_id = register_user_ctr($name, $email, $password, $phone_number, $role);

if ($user_id) {
    $response['status'] = 'success';
    $response['message'] = 'Registered successfully';
    $response['user_id'] = $user_id;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to register';
}

echo json_encode($response);