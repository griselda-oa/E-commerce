<?php
// actions/debug_brands_action.php
header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/db_class.php';

try {
    $db = new db_connection();
    
    // Check if brands table exists and has data
    $result = $db->query("SELECT COUNT(*) as count FROM brands");
    $count = $result->fetch_assoc()['count'];
    
    // Get all brands
    $result = $db->query("SELECT brand_id, brand_name FROM brands ORDER BY brand_name");
    $brands = [];
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'count' => $count,
        'brands' => $brands,
        'message' => "Found {$count} brands in database"
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
