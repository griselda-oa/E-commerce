<?php
// actions/fetch_all_products_action.php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/product_class.php';

try {
    $product = new Product();
    $result = $product->view_all_products();
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
