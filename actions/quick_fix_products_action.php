<?php
// actions/quick_fix_products_action.php
header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/db_class.php';

try {
    $db = new db_connection();
    
    // First, let's check if products table exists
    $result = $db->query("SHOW TABLES LIKE 'products'");
    if ($result->num_rows == 0) {
        // Create products table if it doesn't exist
        $create_table = "CREATE TABLE IF NOT EXISTS products (
            product_id INT AUTO_INCREMENT PRIMARY KEY,
            product_title VARCHAR(200) NOT NULL,
            product_desc TEXT,
            product_price DECIMAL(10,2) NOT NULL,
            product_keywords VARCHAR(500),
            product_image VARCHAR(500),
            product_cat INT,
            product_brand INT,
            FOREIGN KEY (product_cat) REFERENCES categories(cat_id),
            FOREIGN KEY (product_brand) REFERENCES brands(brand_id)
        )";
        $db->query($create_table);
    }
    
    // Check if products table is empty
    $result = $db->query("SELECT COUNT(*) as count FROM products");
    $count = $result->fetch_assoc()['count'];
    
    if ($count == 0) {
        // Insert some sample products
        $products = [
            ['Handwoven Kente Cloth (6 yards)', 'Authentic handwoven Kente cloth from Bonwire', 280.00, 'kente, traditional, handwoven', 1, 1],
            ['Kente Scarf with Adinkra Symbols', 'Elegant Kente scarf featuring traditional Adinkra symbols', 95.00, 'kente, scarf, adinkra', 1, 2],
            ['Carved Adinkra Symbol Wall Art', 'Hand-carved wooden wall art featuring traditional Adinkra symbols', 750.00, 'wood, carving, adinkra', 2, 4],
            ['Ghana Bead Necklace Set', 'Beautiful set of traditional Ghanaian bead necklaces', 320.00, 'beads, necklace, jewelry', 3, 5],
            ['Beaded Handbag with Adinkra Symbols', 'Stylish handbag featuring intricate beadwork', 420.00, 'handbag, beads, adinkra', 4, 7],
            ['Traditional Clay Cooking Pot', 'Authentic clay cooking pot for traditional cuisine', 65.00, 'clay pot, cooking, traditional', 5, 9]
        ];
        
        $stmt = $db->prepare("INSERT INTO products (product_title, product_desc, product_price, product_keywords, product_cat, product_brand) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($products as $product) {
            $stmt->bind_param('ssdssii', $product[0], $product[1], $product[2], $product[3], $product[4], $product[5]);
            $stmt->execute();
        }
    }
    
    // Now get all products with category and brand names
    $sql = "SELECT p.product_id, p.product_title, p.product_desc, p.product_price, p.product_keywords, p.product_image,
                   c.cat_name, b.brand_name, p.product_cat, p.product_brand
            FROM products p
            LEFT JOIN categories c ON p.product_cat = c.cat_id
            LEFT JOIN brands b ON p.product_brand = b.brand_id
            ORDER BY p.product_id DESC";
    
    $result = $db->query($sql);
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $products,
        'message' => 'Products loaded successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
