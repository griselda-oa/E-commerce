<?php
// actions/fetch_category_action.php
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

try {
    $categoryController = new CategoryController();
    $result = $categoryController->get_categories_ctr();
    
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
