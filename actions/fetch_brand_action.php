<?php
// actions/fetch_brand_action.php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Check if required files exist
$core_path = __DIR__ . '/../settings/core.php';
$controller_path = __DIR__ . '/../controllers/brand_controller.php';

if (!file_exists($core_path) || !file_exists($controller_path)) {
    echo json_encode(['success' => false, 'message' => 'Required files missing. Please check file upload.']);
    exit;
}

require_once $core_path;
require_once $controller_path;

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if user is admin
if (!is_admin()) {
    echo json_encode(['success' => false, 'message' => 'Admin privileges required']);
    exit;
}

try {
    $brandController = new BrandController();
    $result = $brandController->get_brands_ctr();
    
    // If no brands found, try quick fix
    if (!$result['success'] || empty($result['data'])) {
        // Try quick fix
        $quick_fix_url = __DIR__ . '/quick_fix_brands_action.php';
        if (file_exists($quick_fix_url)) {
            include $quick_fix_url;
            exit;
        }
    }
    
    echo json_encode($result);
} catch (Exception $e) {
    // Try quick fix as fallback
    $quick_fix_url = __DIR__ . '/quick_fix_brands_action.php';
    if (file_exists($quick_fix_url)) {
        include $quick_fix_url;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
