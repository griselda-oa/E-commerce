<?php
// actions/fetch_category_action.php
header('Content-Type: application/json');

error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

function sendJson($data) {
    echo json_encode($data);
    exit;
}

try {
    // Check if required files exist
    $core_path = __DIR__ . '/../settings/core.php';
    $controller_path = __DIR__ . '/../controllers/category_controller.php';
    
    if (!file_exists($core_path)) {
        sendJson(['success' => false, 'message' => 'core.php missing']);
    }
    if (!file_exists($controller_path)) {
        sendJson(['success' => false, 'message' => 'category_controller.php missing']);
    }
    
    require_once $core_path;
    require_once $controller_path;
    
    // Categories are public - no login required
    // This allows both customer and admin views to work
    
    $categoryController = new CategoryController();
    $result = $categoryController->get_categories_ctr();
    
    // If no categories found, return empty array
    if (!$result || !isset($result['success'])) {
        sendJson(['success' => true, 'data' => [], 'message' => 'No categories found']);
    }
    
    if (!$result['success']) {
        sendJson(['success' => true, 'data' => [], 'message' => $result['message'] ?? 'No categories']);
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
        'data' => []
    ]);
}
?>
