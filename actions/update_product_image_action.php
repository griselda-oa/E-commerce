<?php
// actions/update_product_image_action.php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/db_class.php';

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

$product_id = intval($_POST['product_id'] ?? 0);
$product_image = $_POST['product_image'] ?? '';

if (!$product_id || !$product_image) {
    echo json_encode(['success' => false, 'message' => 'Missing product ID or image path']);
    exit;
}

try {
    $db = new db_connection();
    $sql = "UPDATE products SET product_image = ? WHERE product_id = ?";
    $stmt = $db->db->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $db->db->error]);
        exit;
    }
    $stmt->bind_param('si', $product_image, $product_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Product image updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update product image'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
