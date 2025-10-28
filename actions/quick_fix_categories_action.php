<?php
// actions/quick_fix_categories_action.php
header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/db_class.php';

try {
    $db = new db_connection();
    
    // First, let's check if categories table exists
    $result = $db->query("SHOW TABLES LIKE 'categories'");
    if ($result->num_rows == 0) {
        // Create categories table if it doesn't exist
        $create_table = "CREATE TABLE IF NOT EXISTS categories (
            cat_id INT AUTO_INCREMENT PRIMARY KEY,
            cat_name VARCHAR(100) NOT NULL UNIQUE
        )";
        $db->query($create_table);
    }
    
    // Check if categories table is empty
    $result = $db->query("SELECT COUNT(*) as count FROM categories");
    $count = $result->fetch_assoc()['count'];
    
    if ($count == 0) {
        // Insert some basic categories
        $categories = [
            'Kente & Textiles',
            'Wood Carvings & Sculptures', 
            'Handcrafted Jewelry',
            'Beaded Art & Accessories',
            'Ceramics & Pottery',
            'Leather Goods & Accessories',
            'Metalwork & Brass Art',
            'Basketry & Woven Items',
            'Traditional Musical Instruments',
            'Home Decor & Furnishings'
        ];
        
        $stmt = $db->prepare("INSERT INTO categories (cat_name) VALUES (?)");
        foreach ($categories as $category) {
            $stmt->bind_param('s', $category);
            $stmt->execute();
        }
    }
    
    // Now get all categories
    $result = $db->query("SELECT cat_id, cat_name FROM categories ORDER BY cat_name");
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $categories,
        'message' => 'Categories loaded successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
