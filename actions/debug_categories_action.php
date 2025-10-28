<?php
// actions/debug_categories_action.php
header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/db_class.php';

try {
    $db = new db_connection();
    
    // Check if categories table exists and has data
    $result = $db->query("SELECT COUNT(*) as count FROM categories");
    $count = $result->fetch_assoc()['count'];
    
    // Get all categories
    $result = $db->query("SELECT cat_id, cat_name FROM categories ORDER BY cat_name");
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'count' => $count,
        'categories' => $categories,
        'message' => "Found {$count} categories in database"
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
