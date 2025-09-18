<?php
// actions/register_customer_action.php
header('Content-Type: application/json');
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../controllers/customer_controller.php';

// Prevent registering when already logged in (optional)
if (isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error','message'=>'You are already logged in.']);
    exit;
}

// Collect inputs
$fields = [
  'name'             => trim($_POST['name'] ?? ''),
  'email'            => trim($_POST['email'] ?? ''),
  'password'         => $_POST['password'] ?? '',
  'confirm_password' => $_POST['confirm_password'] ?? '',
  'country'          => trim($_POST['country'] ?? ''),
  'city'             => trim($_POST['city'] ?? ''),
  'phone_number'     => trim($_POST['phone_number'] ?? ''),
  'role'             => isset($_POST['role']) ? (int)$_POST['role'] : 2,
];

$errors = [];

// Server-side validation aligned to DB schema
if ($fields['name'] === '' || mb_strlen($fields['name']) > 100) {
    $errors[] = 'Full name is required (≤100 chars).';
}
if (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($fields['email']) > 50) {
    $errors[] = 'Valid email is required (≤50 chars).';
}
$pw = $fields['password'];
if (
    strlen($pw) < 6 ||
    !preg_match('/[a-z]/', $pw) ||
    !preg_match('/[A-Z]/', $pw) ||
    !preg_match('/[0-9]/', $pw)
) {
    $errors[] = 'Password must be ≥6 chars and include a-z, A-Z, and a number.';
}
if ($fields['password'] !== $fields['confirm_password']) {
    $errors[] = 'Passwords do not match.';
}
if ($fields['country'] === '' || mb_strlen($fields['country']) > 30) {
    $errors[] = 'Country is required (≤30 chars).';
}
if ($fields['city'] === '' || mb_strlen($fields['city']) > 30) {
    $errors[] = 'City is required (≤30 chars).';
}
if (
    $fields['phone_number'] === '' ||
    mb_strlen($fields['phone_number']) > 15 ||
    !preg_match('/^[0-9+\s-]{7,15}$/', $fields['phone_number'])
) {
    $errors[] = 'Contact number must be 7–15 digits (may include + or -).';
}
if (!in_array($fields['role'], [1, 2], true)) {
    $errors[] = 'Invalid role.';
}

if ($errors) {
    echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
    exit;
}

// Email uniqueness
if (get_customer_by_email_ctr($fields['email'])) {
    echo json_encode(['status'=>'error','message'=>'Email already in use.']);
    exit;
}

// Hash password and persist
$hashed = password_hash($fields['password'], PASSWORD_BCRYPT);

$insert_id = register_customer_ctr([
  'name'       => $fields['name'],
  'email'      => $fields['email'],
  'hashed_pass'=> $hashed,
  'country'    => $fields['country'],
  'city'       => $fields['city'],
  'contact'    => $fields['phone_number'],
  'role'       => $fields['role'],
  'image'      => null
]);

if ($insert_id) {
    echo json_encode(['status'=>'success','message'=>'Registered successfully. Please log in.','user_id'=>$insert_id]);
} else {
    // Get the last database error for debugging
    $db_error = '';
    if (isset($GLOBALS['db'])) {
        $db_error = $GLOBALS['db']->error ?? 'Unknown database error';
    }
    echo json_encode(['status'=>'error','message'=>'Registration failed. Database error: ' . $db_error]);
}