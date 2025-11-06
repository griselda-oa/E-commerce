<?php
// actions/download_products_csv_action.php
// Handle CSV download of all products

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/security.php';
require_once __DIR__ . '/../controllers/product_controller.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('HTTP/1.1 401 Unauthorized');
    echo 'Unauthorized';
    exit;
}

// Check if user is admin
if (!is_admin()) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Admin privileges required';
    exit;
}

// Get all products
$productController = new ProductController();
$result = $productController->get_products_ctr();

if (!$result['success'] || empty($result['data'])) {
    header('HTTP/1.1 500 Internal Server Error');
    echo 'Error fetching products';
    exit;
}

$products = $result['data'];

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="products_' . date('Y-m-d_His') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8 (helps Excel recognize encoding)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write CSV headers
$headers = ['product_id', 'product_title', 'product_description', 'product_price', 'product_keyword', 'cat_id', 'brand_id', 'cat_name', 'brand_name', 'product_image'];
fputcsv($output, $headers);

// Write product data
foreach ($products as $product) {
    $row = [
        $product['product_id'] ?? '',
        $product['product_title'] ?? '',
        $product['product_description'] ?? ($product['product_desc'] ?? ''),
        $product['product_price'] ?? '0.00',
        $product['product_keyword'] ?? ($product['product_keywords'] ?? ''),
        $product['cat_id'] ?? '',
        $product['brand_id'] ?? '',
        $product['cat_name'] ?? '',
        $product['brand_name'] ?? '',
        $product['product_image'] ?? ''
    ];
    fputcsv($output, $row);
}

fclose($output);
exit;
?>

