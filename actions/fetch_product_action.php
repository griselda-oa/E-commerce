<?php
// actions/fetch_product_action.php
// MVC-compliant version - uses ProductController
header('Content-Type: application/json');

// Error handling - match working fetch_brand_action.php pattern
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

function sendJson($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    // Check if required files exist - simplified like fetch_brand_action.php
    $core_path = __DIR__ . '/../settings/core.php';
    $controller_path = __DIR__ . '/../controllers/product_controller.php';
    
    if (!file_exists($core_path)) {
        sendJson(['success' => false, 'message' => 'core.php missing']);
    }
    if (!file_exists($controller_path)) {
        sendJson(['success' => false, 'message' => 'product_controller.php missing']);
    }
    
    // Load files - controller will load class, class will load db and security
    require_once $core_path;
    require_once $controller_path;
    
    // Check login
    if (!function_exists('is_logged_in') || !is_logged_in()) {
        sendJson(['success' => false, 'message' => 'User not logged in']);
    }
    
    // Check admin
    if (!function_exists('is_admin') || !is_admin()) {
        sendJson(['success' => false, 'message' => 'Admin privileges required']);
    }
    
    // Use ProductController following MVC pattern
    $productController = new ProductController();
    $result = $productController->get_products_ctr();
    
    // If no products found, return empty array (like fetch_brand_action.php)
    if (!$result || !isset($result['success'])) {
        sendJson(['success' => true, 'data' => [], 'message' => 'No products found']);
    }
    
    if (!$result['success']) {
        sendJson(['success' => true, 'data' => [], 'message' => $result['message'] ?? 'No products']);
    }
    
    // Ensure data is array
    if (!isset($result['data']) || !is_array($result['data'])) {
        $result['data'] = [];
    }
    
    sendJson($result);
    
} catch (Throwable $e) {
    // Catch any error and return detailed message
    sendJson([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine(),
        'data' => []
    ]);
}
?>
