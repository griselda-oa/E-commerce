<?php
// actions/fetch_product_action.php
// MVC-compliant version - uses ProductController
header('Content-Type: application/json');

// Enable error reporting temporarily to catch fatal errors
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Register error handler to catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Fatal Error: ' . $error['message'],
            'file' => basename($error['file']),
            'line' => $error['line'],
            'data' => []
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
});

function sendJson($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    // Check if required files exist - simplified like fetch_brand_action.php
    $core_path = __DIR__ . '/../settings/core.php';
    $controller_path = __DIR__ . '/../controllers/product_controller.php';
    
    if (!file_exists($core_path)) {
        sendJson(['success' => false, 'message' => 'core.php missing at: ' . $core_path]);
    }
    if (!file_exists($controller_path)) {
        sendJson(['success' => false, 'message' => 'product_controller.php missing at: ' . $controller_path]);
    }
    
    // Load files - controller will load class, class will load db and security
    require_once $core_path;
    require_once $controller_path;
    
    // Check if classes exist
    if (!class_exists('ProductController')) {
        sendJson(['success' => false, 'message' => 'ProductController class not found']);
    }
    
    // Check login
    if (!function_exists('is_logged_in') || !is_logged_in()) {
        sendJson(['success' => false, 'message' => 'User not logged in']);
    }
    
    // Check admin
    if (!function_exists('is_admin') || !is_admin()) {
        sendJson(['success' => false, 'message' => 'Admin privileges required']);
    }
    
    // Use ProductController following MVC pattern
    try {
        $productController = new ProductController();
    } catch (Exception $e) {
        sendJson(['success' => false, 'message' => 'Failed to create ProductController: ' . $e->getMessage()]);
    }
    
    try {
        $result = $productController->get_products_ctr();
    } catch (Exception $e) {
        sendJson(['success' => false, 'message' => 'Failed to get products: ' . $e->getMessage()]);
    }
    
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
        'trace' => $e->getTraceAsString(),
        'data' => []
    ]);
}
?>
