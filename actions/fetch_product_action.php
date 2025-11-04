<?php
// actions/fetch_product_action.php
// Simple, bulletproof version
header('Content-Type: application/json');

// Turn off all output
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

function sendJson($data) {
    echo json_encode($data);
    exit;
}

try {
    // Check and load required files
    $core_path = __DIR__ . '/../settings/core.php';
    $db_class_path = __DIR__ . '/../settings/db_class.php';
    $security_path = __DIR__ . '/../settings/security.php';
    $controller_path = __DIR__ . '/../controllers/product_controller.php';
    
    if (!file_exists($core_path)) {
        sendJson(['success' => false, 'message' => 'core.php missing']);
    }
    if (!file_exists($db_class_path)) {
        sendJson(['success' => false, 'message' => 'db_class.php missing']);
    }
    if (!file_exists($security_path)) {
        sendJson(['success' => false, 'message' => 'security.php missing']);
    }
    if (!file_exists($controller_path)) {
        sendJson(['success' => false, 'message' => 'product_controller.php missing']);
    }
    
    require_once $core_path;
    require_once $db_class_path;
    require_once $security_path;
    require_once $controller_path;
    
    // Check login
    if (!function_exists('is_logged_in') || !is_logged_in()) {
        sendJson(['success' => false, 'message' => 'User not logged in']);
    }
    
    // Check admin
    if (!function_exists('is_admin') || !is_admin()) {
        sendJson(['success' => false, 'message' => 'Admin privileges required']);
    }
    
    // Get products - use direct Product class for consistency
    $product = new Product();
    $result = $product->getAllProducts();
    
    // Handle result
    if (!$result || !isset($result['success'])) {
        sendJson(['success' => true, 'data' => [], 'message' => 'No products found']);
    }
    
    // If failed but has structure, return empty
    if (!$result['success']) {
        sendJson(['success' => true, 'data' => [], 'message' => $result['message'] ?? 'No products']);
    }
    
    // Ensure data is array
    if (!isset($result['data']) || !is_array($result['data'])) {
        $result['data'] = [];
    }
    
    sendJson($result);
    
} catch (Throwable $e) {
    // Catch any error
    sendJson([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage() . ' | File: ' . basename($e->getFile()) . ' | Line: ' . $e->getLine(),
        'data' => []
    ]);
}
?>
