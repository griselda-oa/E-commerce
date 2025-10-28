<?php
// actions/test_brands_direct.php
header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/db_class.php';

try {
    $db = new db_connection();
    
    // Get all brands
    $result = $db->query("SELECT brand_id, brand_name FROM brands ORDER BY brand_name");
    $brands = [];
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $brands,
        'count' => count($brands),
        'message' => 'Direct brand test successful'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Direct brand test failed: ' . $e->getMessage()
    ]);
}
?>
