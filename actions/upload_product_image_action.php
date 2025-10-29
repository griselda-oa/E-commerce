<?php
// actions/upload_product_image_action.php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if user is admin
if (!is_admin()) {
    echo json_encode(['success' => false, 'message' => 'Admin privileges required']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['product_image'];
$user_id = get_user_id();
$product_id = intval($_POST['product_id'] ?? 0);

// Validate file type
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, and GIF images are allowed']);
    exit;
}

// Validate file size (max 5MB)
$max_size = 5 * 1024 * 1024; // 5MB in bytes
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit']);
    exit;
}

// Create directory structure: uploads/u{user_id}/p{product_id}/
$upload_dir = __DIR__ . '/../uploads/u' . $user_id;
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory. Please check permissions.']);
        exit;
    }
}

$product_dir = $upload_dir . '/p' . $product_id;
if (!is_dir($product_dir)) {
    if (!mkdir($product_dir, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create product directory. Please check permissions.']);
        exit;
    }
}

// Generate unique filename
$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$timestamp = time();
$new_filename = 'image_' . $timestamp . '.' . $file_extension;
$upload_path = $product_dir . '/' . $new_filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
    echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
    exit;
}

// Return relative path for database storage
$relative_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $new_filename;

echo json_encode([
    'success' => true,
    'message' => 'Image uploaded successfully',
    'image_path' => $relative_path
]);
?>
