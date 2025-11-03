<?php
// actions/fetch_product_action.php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$core_path = __DIR__ . '/../settings/core.php';
$controller_path = __DIR__ . '/../controllers/product_controller.php';

if (!file_exists($core_path) || !file_exists($controller_path)) {
    echo json_encode(['success' => false, 'message' => 'Required files missing.']);
    exit;
}

require_once $core_path;
require_once $controller_path;

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if (!is_admin()) {
    echo json_encode(['success' => false, 'message' => 'Admin privileges required']);
    exit;
}

try {
    $productController = new ProductController();
    $result = $productController->get_products_ctr();
    
    // If no products found, return empty array instead of error
    if (!$result || !isset($result['success'])) {
        echo json_encode(['success' => true, 'data' => [], 'message' => 'No products found']);
        exit;
    }
    
    if (!$result['success']) {
        // If query failed but returned a result structure, return empty array
        echo json_encode(['success' => true, 'data' => [], 'message' => $result['message'] ?? 'No products found']);
        exit;
    }
    
    // Ensure data is always an array
    if (!isset($result['data']) || !is_array($result['data'])) {
        $result['data'] = [];
    }
    
    echo json_encode($result);
} catch (Exception $e) {
    // Log the error for debugging
    error_log('Product fetch error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error loading products: ' . $e->getMessage(),
        'data' => []
    ]);
} catch (Error $e) {
    // Catch fatal errors too
    error_log('Product fetch fatal error: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Fatal error: ' . $e->getMessage(),
        'data' => []
    ]);
}
?>
