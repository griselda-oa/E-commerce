<?php
// actions/fetch_product_action.php
// Start output buffering to catch any errors
ob_start();

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$core_path = __DIR__ . '/../settings/core.php';
$controller_path = __DIR__ . '/../controllers/product_controller.php';
$db_class_path = __DIR__ . '/../settings/db_class.php';
$security_path = __DIR__ . '/../settings/security.php';

// Check all required files
$missing_files = array();
if (!file_exists($core_path)) $missing_files[] = 'core.php';
if (!file_exists($controller_path)) $missing_files[] = 'product_controller.php';
if (!file_exists($db_class_path)) $missing_files[] = 'db_class.php';
if (!file_exists($security_path)) $missing_files[] = 'security.php';

if (!empty($missing_files)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Required files missing: ' . implode(', ', $missing_files)]);
    exit;
}

try {
    require_once $core_path;
    require_once $db_class_path;
    require_once $security_path;
    require_once $controller_path;
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Error loading files: ' . $e->getMessage()]);
    exit;
} catch (Error $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Fatal error loading files: ' . $e->getMessage()]);
    exit;
}

if (!is_logged_in()) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if (!is_admin()) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Admin privileges required']);
    exit;
}

try {
    $productController = new ProductController();
    $result = $productController->get_products_ctr();
    
    // Clean output buffer before sending JSON
    ob_clean();
    
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
    
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error loading products: ' . $e->getMessage() . ' | Line: ' . $e->getLine(),
        'data' => []
    ]);
} catch (Error $e) {
    // Catch fatal errors too
    error_log('Product fetch fatal error: ' . $e->getMessage());
    
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Fatal error: ' . $e->getMessage() . ' | Line: ' . $e->getLine(),
        'data' => []
    ]);
}
?>
