<?php
// Quick test to see what's happening
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = array('step' => '', 'status' => 'ok', 'message' => '');

try {
    $response['step'] = '1. Checking files';
    $core_path = __DIR__ . '/../settings/core.php';
    $db_path = __DIR__ . '/../settings/db_class.php';
    $controller_path = __DIR__ . '/../controllers/product_controller.php';
    
    if (!file_exists($core_path)) {
        throw new Exception('core.php missing');
    }
    if (!file_exists($db_path)) {
        throw new Exception('db_class.php missing');
    }
    if (!file_exists($controller_path)) {
        throw new Exception('product_controller.php missing');
    }
    
    $response['step'] = '2. Loading files';
    require_once $core_path;
    require_once $db_path;
    require_once $controller_path;
    
    $response['step'] = '3. Checking session';
    if (!function_exists('is_logged_in')) {
        throw new Exception('is_logged_in function missing');
    }
    if (!is_logged_in()) {
        throw new Exception('Not logged in');
    }
    
    $response['step'] = '4. Checking admin';
    if (!function_exists('is_admin')) {
        throw new Exception('is_admin function missing');
    }
    if (!is_admin()) {
        throw new Exception('Not admin');
    }
    
    $response['step'] = '5. Creating controller';
    $productController = new ProductController();
    
    $response['step'] = '6. Fetching products';
    $result = $productController->get_products_ctr();
    
    $response['success'] = true;
    $response['result'] = $result;
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['error'] = $e->getMessage();
    $response['file'] = $e->getFile();
    $response['line'] = $e->getLine();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>

