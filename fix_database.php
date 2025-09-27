<?php
// fix_database.php - Quick database fix for categories table
require_once __DIR__ . '/settings/db_class.php';

echo "<h1>Database Fix for Category Management</h1>";

try {
    // Connect to database
    $db = new db_connection();
    echo "✅ Database connection successful<br><br>";
    
    // Check if user_id column exists
    $result = $db->db->query("DESCRIBE categories");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    if (in_array('user_id', $columns)) {
        echo "✅ Categories table already has user_id column<br>";
        echo "🎉 Your database is ready for category management!<br>";
    } else {
        echo "❌ Categories table missing user_id column<br>";
        echo "🔧 Running database updates...<br><br>";
        
        // Add user_id column
        $sql1 = "ALTER TABLE `categories` ADD COLUMN `user_id` int(11) NOT NULL AFTER `cat_id`";
        if ($db->db->query($sql1)) {
            echo "✅ Added user_id column<br>";
        } else {
            echo "❌ Failed to add user_id column: " . $db->db->error . "<br>";
        }
        
        // Add foreign key constraint
        $sql2 = "ALTER TABLE `categories` ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE";
        if ($db->db->query($sql2)) {
            echo "✅ Added foreign key constraint<br>";
        } else {
            echo "⚠️ Foreign key constraint failed (may already exist): " . $db->db->error . "<br>";
        }
        
        // Add index
        $sql3 = "ALTER TABLE `categories` ADD KEY `user_id` (`user_id`)";
        if ($db->db->query($sql3)) {
            echo "✅ Added user_id index<br>";
        } else {
            echo "⚠️ Index creation failed (may already exist): " . $db->db->error . "<br>";
        }
        
        // Add unique constraint
        $sql4 = "ALTER TABLE `categories` ADD UNIQUE KEY `unique_cat_name_per_user` (`cat_name`, `user_id`)";
        if ($db->db->query($sql4)) {
            echo "✅ Added unique constraint<br>";
        } else {
            echo "⚠️ Unique constraint failed (may already exist): " . $db->db->error . "<br>";
        }
        
        echo "<br>🎉 Database update completed!<br>";
    }
    
    // Test the category functionality
    echo "<br><h2>Testing Category Functionality</h2>";
    
    // Get admin user ID
    $result = $db->db->query("SELECT customer_id FROM customer WHERE user_role = 1 LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $admin_id = $row['customer_id'];
        echo "✅ Found admin user ID: $admin_id<br>";
        
        // Test adding a sample category
        $test_cat = "Test Category " . date('H:i:s');
        $sql = "INSERT INTO categories (cat_name, user_id) VALUES (?, ?)";
        $stmt = $db->db->prepare($sql);
        $stmt->bind_param("si", $test_cat, $admin_id);
        
        if ($stmt->execute()) {
            $cat_id = $db->db->insert_id;
            echo "✅ Successfully added test category: '$test_cat' (ID: $cat_id)<br>";
            
            // Test fetching categories
            $sql = "SELECT * FROM categories WHERE user_id = ?";
            $stmt = $db->db->prepare($sql);
            $stmt->bind_param("i", $admin_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $categories = $result->fetch_all(MYSQLI_ASSOC);
            
            echo "✅ Successfully fetched " . count($categories) . " categories<br>";
            
            // Clean up test category
            $sql = "DELETE FROM categories WHERE cat_id = ?";
            $stmt = $db->db->prepare($sql);
            $stmt->bind_param("i", $cat_id);
            $stmt->execute();
            echo "✅ Cleaned up test category<br>";
            
        } else {
            echo "❌ Failed to add test category: " . $stmt->error . "<br>";
        }
    } else {
        echo "❌ No admin user found<br>";
    }
    
    echo "<br><h2>✅ Database Fix Complete!</h2>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Login as admin: <a href='login/login.php'>Login Page</a></li>";
    echo "<li>Go to category management: <a href='admin/category.php'>Category Management</a></li>";
    echo "<li>Test adding, editing, and deleting categories</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><em>Database fix completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>

