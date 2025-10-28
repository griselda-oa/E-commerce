<?php
// actions/add_product_action.php
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
$product_title = trim($_POST['product_title'] ?? '');
$product_description = trim($_POST['product_description'] ?? '');
$product_price = floatval($_POST['product_price'] ?? 0);
$product_keyword = trim($_POST['product_keyword'] ?? '');
$product_image = $_POST['product_image'] ?? null;
$cat_id = intval($_POST['cat_id'] ?? 0);
$brand_id = intval($_POST['brand_id'] ?? 0);

// Validation
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
    $result = $productController->add_product_ctr([
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
