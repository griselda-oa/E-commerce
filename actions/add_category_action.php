<?php
// actions/add_category_action.php
header('Content-Type: application/json');

// Disable error display to prevent HTML in JSON response
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Check if required files exist
$core_path = __DIR__ . '/../settings/core.php';
$controller_path = __DIR__ . '/../controllers/category_controller.php';

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
$category_name = trim($_POST['category_name'] ?? '');

if (empty($category_name)) {
    echo json_encode(['success' => false, 'message' => 'Category name is required']);
    exit;
}

if (strlen($category_name) > 100) {
    echo json_encode(['success' => false, 'message' => 'Category name must be 100 characters or less']);
    exit;
}

try {
    $categoryController = new CategoryController();
    $result = $categoryController->add_category_ctr([
        'category_name' => $category_name,
        'user_id' => get_user_id()
    ]);
    
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
