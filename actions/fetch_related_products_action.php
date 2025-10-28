<?php
// actions/fetch_related_products_action.php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/product_class.php';

$product_id = intval($_POST['product_id'] ?? 0);
$cat_id = intval($_POST['cat_id'] ?? 0);

try {
    $product = new Product();
    $result = $product->filter_products_by_category($cat_id);
    
    // Remove the current product from results
    if ($result['success']) {
        $result['data'] = array_filter($result['data'], function($p) use ($product_id) {
            return $p['product_id'] != $product_id;
        });
    }
    
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
