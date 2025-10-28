<?php
// actions/quick_fix_brands_action.php
header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/db_class.php';

try {
    $db = new db_connection();
    
    // First, let's check if brands table exists
    $result = $db->query("SHOW TABLES LIKE 'brands'");
    if ($result->num_rows == 0) {
        // Create brands table if it doesn't exist
        $create_table = "CREATE TABLE IF NOT EXISTS brands (
            brand_id INT AUTO_INCREMENT PRIMARY KEY,
            brand_name VARCHAR(100) NOT NULL UNIQUE
        )";
        $db->query($create_table);
    }
    
    // Check if brands table is empty
    $result = $db->query("SELECT COUNT(*) as count FROM brands");
    $count = $result->fetch_assoc()['count'];
    
    if ($count == 0) {
        // Insert some basic brands
        $brands = [
            'Bonwire Kente Weavers',
            'Adinkra Artisans Guild',
            'Ashanti Traditional Textiles',
            'Contemporary Craft Designs',
            'Ghana Bead Crafts',
            'Traditional Wood Carvers',
            'Handcrafted Jewelry Co',
            'Ceramic Art Studio',
            'Leather Works Ghana',
            'Brass Art Collective'
        ];
        
        $stmt = $db->prepare("INSERT INTO brands (brand_name) VALUES (?)");
        foreach ($brands as $brand) {
            $stmt->bind_param('s', $brand);
            $stmt->execute();
        }
    }
    
    // Now get all brands
    $result = $db->query("SELECT brand_id, brand_name FROM brands ORDER BY brand_name");
    $brands = [];
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $brands,
        'message' => 'Brands loaded successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
