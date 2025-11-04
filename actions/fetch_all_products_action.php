<?php
// actions/fetch_all_products_action.php
header('Content-Type: application/json');

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

function sendJson($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    // Check if required files exist
    $core_path = __DIR__ . '/../settings/core.php';
    $db_class_path = __DIR__ . '/../settings/db_class.php';
    $security_path = __DIR__ . '/../settings/security.php';
    $class_path = __DIR__ . '/../classes/product_class.php';
    
    if (!file_exists($core_path)) {
        sendJson(['success' => false, 'message' => 'core.php not found']);
    }
    if (!file_exists($db_class_path)) {
        sendJson(['success' => false, 'message' => 'db_class.php not found']);
    }
    if (!file_exists($security_path)) {
        sendJson(['success' => false, 'message' => 'security.php not found']);
    }
    if (!file_exists($class_path)) {
        sendJson(['success' => false, 'message' => 'product_class.php not found']);
    }
    
    require_once $core_path;
    require_once $db_class_path;
    require_once $security_path;
    require_once $class_path;
    
    // Initialize product
    $product = new Product();
    
    // Get all products
    $result = $product->getAllProducts();
    
    // Ensure result is in correct format
    if (!isset($result['success'])) {
        $result = ['success' => false, 'message' => 'Unexpected response format', 'data' => []];
    }
    
    // Ensure data is array
    if (!isset($result['data']) || !is_array($result['data'])) {
        $result['data'] = [];
    }
    
    sendJson($result);
    
} catch (Throwable $e) {
    sendJson([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage(),
        'file' => basename($e->getFile()),
        'line' => $e->getLine()
    ]);
}
?>
