<?php
// actions/update_product_action.php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Check if required files exist
$core_path = __DIR__ . '/../settings/core.php';
$controller_path = __DIR__ . '/../controllers/product_controller.php';

if (!file_exists($core_path) || !file_exists($controller_path)) {
    echo json_encode(['success' => false, 'message' => 'Required files missing. Please check file upload.']);
    exit;
}

require_once $core_path;
require_once $controller_path;

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

// Get and validate input
$product_id = intval($_POST['product_id'] ?? 0);
$product_title = trim($_POST['product_title'] ?? '');
$product_description = trim($_POST['product_description'] ?? '');
$product_price = floatval($_POST['product_price'] ?? 0);
$product_keyword = trim($_POST['product_keyword'] ?? '');
$cat_id = intval($_POST['cat_id'] ?? 0);
$brand_id = intval($_POST['brand_id'] ?? 0);

// Handle image upload if provided
$product_image = null;
if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['product_image'];
    $user_id = get_user_id();
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, and GIF images are allowed']);
        exit;
    }
    
    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit']);
        exit;
    }
    
    // Create proper directory structure
    $upload_dir = __DIR__ . '/../uploads/u' . $user_id;
    // if (!is_dir($upload_dir)) {
    //     if (!mkdir($upload_dir, 0777, true)) {
    //         echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
    //         exit;
    //     }
    // }
    
    $product_dir = $upload_dir . '/p' . $product_id;
    if (!is_dir($product_dir)) {
        if (!mkdir($product_dir, 0777, true)) {
            echo json_encode(['success' => false, 'message' => 'Failed to create product directory']);
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
    
    // Store relative path for database
    $product_image = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $new_filename;
} else {
    // Keep existing image if no new image uploaded
    $product_image = $_POST['existing_product_image'] ?? null;
}

// Validation
if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

if (empty($product_title)) {
    echo json_encode(['success' => false, 'message' => 'Product title is required']);
    exit;
}

if (empty($product_description)) {
    echo json_encode(['success' => false, 'message' => 'Product description is required']);
    exit;
}

if ($product_price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Product price must be greater than 0']);
    exit;
}

if ($cat_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please select a category']);
    exit;
}

if ($brand_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please select a brand']);
    exit;
}

try {
    $productController = new ProductController();
    $result = $productController->update_product_ctr([
        'product_id' => $product_id,
        'product_title' => $product_title,
        'product_description' => $product_description,
        'product_price' => $product_price,
        'product_keyword' => $product_keyword,
        'product_image' => $product_image,
        'cat_id' => $cat_id,
        'brand_id' => $brand_id,
        'csrf_token' => $_POST['csrf_token'] ?? ''
    ]);
    
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
