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
    
    // If no products found, try quick fix
    if (!$result['success'] || empty($result['data'])) {
        // Try quick fix
        $quick_fix_url = __DIR__ . '/quick_fix_products_action.php';
        if (file_exists($quick_fix_url)) {
            include $quick_fix_url;
            exit;
        }
    }
    
    echo json_encode($result);
} catch (Exception $e) {
    // Try quick fix as fallback
    $quick_fix_url = __DIR__ . '/quick_fix_products_action.php';
    if (file_exists($quick_fix_url)) {
        include $quick_fix_url;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
