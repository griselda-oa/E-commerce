<?php
// actions/bulk_upload_images_action.php
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

// Check if files were uploaded
// Handle both single and multiple file uploads
if (!isset($_FILES['product_images']) || 
    (!is_array($_FILES['product_images']['name']) && empty($_FILES['product_images']['name']))) {
    echo json_encode(['success' => false, 'message' => 'No files uploaded']);
    exit;
}

$user_id = get_user_id();
$product_id = intval($_POST['product_id'] ?? 0);

if (!$product_id || $product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

// Verify uploads/ directory exists and is within the allowed path
$base_upload_dir = __DIR__ . '/../uploads';
if (!is_dir($base_upload_dir)) {
    echo json_encode(['success' => false, 'message' => 'Upload directory does not exist']);
    exit;
}

// Create directory structure: uploads/u{user_id}/p{product_id}/
$upload_dir = $base_upload_dir . '/u' . $user_id;
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create user upload directory']);
        exit;
    }
}

$product_dir = $upload_dir . '/p' . $product_id;
if (!is_dir($product_dir)) {
    if (!mkdir($product_dir, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create product directory']);
        exit;
    }
}

// Validate that the path is inside uploads/ directory (security check)
$real_product_dir = realpath($product_dir);
$real_base_dir = realpath($base_upload_dir);
if (strpos($real_product_dir, $real_base_dir) !== 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid upload path - security violation']);
    exit;
}

// Process multiple files
// When using multiple file input, PHP restructures $_FILES array
if (isset($_FILES['product_images']) && is_array($_FILES['product_images']['name'])) {
    $files = $_FILES['product_images'];
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid file upload format']);
    exit;
}

$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
$max_size = 5 * 1024 * 1024; // 5MB per file
$uploaded_images = [];
$errors = [];

// Count number of files
$file_count = count($files['name']);

// Process each file
for ($i = 0; $i < $file_count; $i++) {
    // Skip empty file slots
    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
        if ($files['error'][$i] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'File ' . ($i + 1) . ': Upload error';
        }
        continue;
    }
    
    // Validate file type
    if (!in_array($files['type'][$i], $allowed_types)) {
        $errors[] = $files['name'][$i] . ': Invalid file type. Only JPEG, PNG, and GIF allowed';
        continue;
    }
    
    // Validate file size
    if ($files['size'][$i] > $max_size) {
        $errors[] = $files['name'][$i] . ': File size exceeds 5MB limit';
        continue;
    }
    
    // Generate unique filename
    $file_extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
    $timestamp = time() . '_' . $i;
    $new_filename = 'image_' . $timestamp . '.' . $file_extension;
    $upload_path = $product_dir . '/' . $new_filename;
    
    // Move uploaded file
    if (!move_uploaded_file($files['tmp_name'][$i], $upload_path)) {
        $errors[] = $files['name'][$i] . ': Failed to upload';
        continue;
    }
    
    // Store relative path
    $relative_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $new_filename;
    $uploaded_images[] = [
        'filename' => $files['name'][$i],
        'path' => $relative_path,
        'size' => $files['size'][$i]
    ];
}

// Return results
if (empty($uploaded_images)) {
    echo json_encode([
        'success' => false,
        'message' => 'No images were uploaded successfully',
        'errors' => $errors
    ]);
    exit;
}

// If some succeeded and some failed, return partial success
if (!empty($errors)) {
    echo json_encode([
        'success' => true,
        'message' => count($uploaded_images) . ' image(s) uploaded successfully. ' . count($errors) . ' error(s)',
        'images' => $uploaded_images,
        'errors' => $errors,
        'partial' => true
    ]);
    exit;
}

// All successful
echo json_encode([
    'success' => true,
    'message' => count($uploaded_images) . ' image(s) uploaded successfully',
    'images' => $uploaded_images,
    'count' => count($uploaded_images)
]);
?>

