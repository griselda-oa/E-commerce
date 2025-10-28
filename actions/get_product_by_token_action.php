<?php
// actions/get_product_by_token_action.php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/security.php';
require_once __DIR__ . '/../controllers/product_controller.php';

// Rate limiting
if (!SecurityManager::checkRateLimit('product_access', 100, 3600)) {
    echo json_encode(['success' => false, 'message' => 'Too many requests. Please try again later.']);
    exit;
}

$token = SecurityManager::sanitizeString($_GET['token'] ?? '', 500);

if (empty($token)) {
    echo json_encode(['success' => false, 'message' => 'Product token is required']);
    exit;
}

try {
    $productController = new ProductController();
    $result = $productController->get_product_by_token_ctr($token);
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
