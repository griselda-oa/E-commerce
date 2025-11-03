<?php
// actions/composite_search_action.php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

try {
    // Get search filters from POST
    $filters = array();
    
    // Keyword search
    if (!empty($_POST['keyword'])) {
        $filters['keyword'] = trim($_POST['keyword']);
    }
    
    // Category filter
    if (!empty($_POST['cat_id']) && $_POST['cat_id'] != '') {
        $filters['cat_id'] = intval($_POST['cat_id']);
    }
    
    // Brand filter
    if (!empty($_POST['brand_id']) && $_POST['brand_id'] != '') {
        $filters['brand_id'] = intval($_POST['brand_id']);
    }
    
    // Price range filters
    if (!empty($_POST['min_price']) && $_POST['min_price'] != '') {
        $filters['min_price'] = floatval($_POST['min_price']);
    }
    
    if (!empty($_POST['max_price']) && $_POST['max_price'] != '') {
        $filters['max_price'] = floatval($_POST['max_price']);
    }
    
    // If no filters provided, return all products
    if (empty($filters)) {
        $productController = new ProductController();
        $result = $productController->get_products_ctr();
        echo json_encode($result);
        exit;
    }
    
    // Perform composite search
    $productController = new ProductController();
    $result = $productController->composite_search_ctr($filters);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>

