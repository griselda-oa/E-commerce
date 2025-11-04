<?php
// Quick debug to see what's happening
header('Content-Type: text/plain');

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== PRODUCT DEBUG ===\n\n";

try {
    echo "1. Loading core.php...\n";
    require_once __DIR__ . '/../settings/core.php';
    echo "   ✓ core.php loaded\n";
    
    echo "2. Loading db_class.php...\n";
    require_once __DIR__ . '/../settings/db_class.php';
    echo "   ✓ db_class.php loaded\n";
    
    echo "3. Loading security.php...\n";
    require_once __DIR__ . '/../settings/security.php';
    echo "   ✓ security.php loaded\n";
    
    echo "4. Loading product_class.php...\n";
    require_once __DIR__ . '/../classes/product_class.php';
    echo "   ✓ product_class.php loaded\n";
    
    echo "5. Creating Product instance...\n";
    $product = new Product();
    echo "   ✓ Product instance created\n";
    
    echo "6. Calling getAllProducts()...\n";
    $result = $product->getAllProducts();
    echo "   Result:\n";
    print_r($result);
    
    echo "\n7. Checking database directly...\n";
    $db = new db_connection();
    $query = $db->query("SELECT COUNT(*) as count FROM products");
    if ($query) {
        $row = $query->fetch_assoc();
        echo "   Products in database: " . $row['count'] . "\n";
    } else {
        echo "   Error: " . $db->error . "\n";
    }
    
    echo "\n8. Testing simple query...\n";
    $query = $db->query("SELECT product_id, product_title FROM products LIMIT 5");
    if ($query) {
        echo "   Sample products:\n";
        while ($row = $query->fetch_assoc()) {
            echo "   - ID: " . $row['product_id'] . ", Title: " . $row['product_title'] . "\n";
        }
    } else {
        echo "   Error: " . $db->error . "\n";
    }
    
} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
} catch (Error $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>

