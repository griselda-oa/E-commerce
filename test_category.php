<?php
// test_category.php - Test script for category functionality
require_once __DIR__ . '/settings/core.php';
require_once __DIR__ . '/classes/category_class.php';
require_once __DIR__ . '/controllers/category_controller.php';

echo "<h1>Category Management System Test</h1>";

// Test 1: Database Connection
echo "<h2>Test 1: Database Connection</h2>";
try {
    $category = new Category();
    echo "✅ Database connection successful<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit();
}

// Test 2: Check if categories table has user_id column
echo "<h2>Test 2: Database Schema Check</h2>";
try {
    $result = $category->db->query("DESCRIBE categories");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    if (in_array('user_id', $columns)) {
        echo "✅ Categories table has user_id column<br>";
    } else {
        echo "❌ Categories table missing user_id column. Please run the SQL update script.<br>";
        echo "<strong>Required SQL:</strong><br>";
        echo "<pre>";
        echo "ALTER TABLE `categories` ADD COLUMN `user_id` int(11) NOT NULL AFTER `cat_id`;\n";
        echo "ALTER TABLE `categories` ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;\n";
        echo "ALTER TABLE `categories` ADD KEY `user_id` (`user_id`);\n";
        echo "ALTER TABLE `categories` ADD UNIQUE KEY `unique_cat_name_per_user` (`cat_name`, `user_id`);";
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "❌ Error checking database schema: " . $e->getMessage() . "<br>";
}

// Test 3: Controller functionality
echo "<h2>Test 3: Controller Functionality</h2>";
try {
    $controller = new CategoryController();
    echo "✅ Category controller initialized successfully<br>";
    
    // Test with dummy data (won't actually add to database without valid user)
    $testData = [
        'cat_name' => 'Test Category',
        'user_id' => 999999 // Non-existent user ID
    ];
    
    $result = $controller->add_category_ctr($testData);
    if ($result['status'] === 'error') {
        echo "✅ Controller properly validates user existence<br>";
    } else {
        echo "❌ Controller should have rejected invalid user ID<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Controller test failed: " . $e->getMessage() . "<br>";
}

// Test 4: Check if admin user exists
echo "<h2>Test 4: Admin User Check</h2>";
try {
    $result = $category->db->query("SELECT COUNT(*) as count FROM customer WHERE user_role = 1");
    $row = $result->fetch_assoc();
    $adminCount = $row['count'];
    
    if ($adminCount > 0) {
        echo "✅ Found $adminCount admin user(s) in database<br>";
        
        // Show admin users
        $result = $category->db->query("SELECT customer_id, customer_name, customer_email FROM customer WHERE user_role = 1 LIMIT 5");
        echo "<strong>Admin Users:</strong><br>";
        while ($row = $result->fetch_assoc()) {
            echo "- ID: {$row['customer_id']}, Name: {$row['customer_name']}, Email: {$row['customer_email']}<br>";
        }
    } else {
        echo "❌ No admin users found. Please create an admin user first.<br>";
        echo "<strong>To create an admin user:</strong><br>";
        echo "1. Register a new user through the registration form<br>";
        echo "2. Update their user_role to 1 in the database:<br>";
        echo "<pre>UPDATE customer SET user_role = 1 WHERE customer_email = 'your_email@example.com';</pre>";
    }
} catch (Exception $e) {
    echo "❌ Error checking admin users: " . $e->getMessage() . "<br>";
}

echo "<h2>Test Summary</h2>";
echo "<p>If all tests pass, your category management system should be ready to use!</p>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>1. Run the database update script if needed</li>";
echo "<li>2. Create an admin user if none exists</li>";
echo "<li>3. Login as admin and navigate to <a href='admin/category.php'>admin/category.php</a></li>";
echo "<li>4. Test adding, editing, and deleting categories</li>";
echo "</ul>";

echo "<hr>";
echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>

