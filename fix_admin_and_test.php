<?php
// fix_admin_and_test.php - Fix admin status and test category system
session_start();
require_once 'settings/db_class.php';
require_once 'settings/core.php';
require_once 'controllers/category_controller.php';

echo "<h1>Admin Fix & Category System Test</h1>";
echo "<hr>";

// Check if user is logged in
if (!isLoggedIn()) {
    echo "<p>❌ Please login first</p>";
    echo "<p><a href='login/login.php'>Go to Login</a></p>";
    exit();
}

$user_id = currentUserId();
$user_name = $_SESSION['user_name'] ?? 'User';

echo "<h2>Current Status</h2>";
echo "<p><strong>User ID:</strong> $user_id</p>";
echo "<p><strong>User Name:</strong> $user_name</p>";
echo "<p><strong>Is Logged In:</strong> " . (isLoggedIn() ? 'Yes' : 'No') . "</p>";
echo "<p><strong>Is Admin:</strong> " . (isAdmin() ? 'Yes' : 'No') . "</p>";
echo "<p><strong>User Role:</strong> " . ($_SESSION['user_role'] ?? 'Not set') . "</p>";

// Make user admin if not already
if (!isAdmin()) {
    echo "<hr><h2>Making User Admin</h2>";
    
    try {
        $db = new db_connection();
        
        // Update user role to admin (1)
        $sql = "UPDATE customer SET user_role = 1 WHERE customer_id = ?";
        $stmt = $db->db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "<p>✅ Successfully updated user to ADMIN role!</p>";
                
                // Update session
                $_SESSION['user_role'] = 1;
                
                echo "<p>🎉 You are now an ADMIN user!</p>";
            } else {
                echo "<p>⚠️ No rows were updated. User might already be admin.</p>";
            }
        } else {
            echo "<p>❌ Failed to update user role: " . $stmt->error . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    }
}

// Refresh admin status
echo "<hr><h2>Updated Status</h2>";
echo "<p><strong>Is Admin:</strong> " . (isAdmin() ? 'Yes' : 'No') . "</p>";
echo "<p><strong>User Role:</strong> " . ($_SESSION['user_role'] ?? 'Not set') . "</p>";

// Test category system
if (isAdmin()) {
    echo "<hr><h2>Testing Category System</h2>";
    
    try {
        $categoryController = new CategoryController();
        
        // Test 1: Add a test category
        echo "<h3>1. Testing Add Category</h3>";
        $testData = [
            'cat_name' => 'Test Category ' . date('Y-m-d H:i:s'),
            'user_id' => $user_id
        ];
        
        $addResult = $categoryController->add_category_ctr($testData);
        echo "<p><strong>Add Result:</strong> " . json_encode($addResult) . "</p>";
        
        // Test 2: Fetch categories
        echo "<h3>2. Testing Fetch Categories</h3>";
        $fetchData = ['user_id' => $user_id];
        $fetchResult = $categoryController->fetch_categories_ctr($fetchData);
        echo "<p><strong>Fetch Result:</strong> " . json_encode($fetchResult) . "</p>";
        
        // Test 3: Test update if we have categories
        if ($fetchResult['status'] === 'success' && !empty($fetchResult['categories'])) {
            echo "<h3>3. Testing Update Category</h3>";
            $firstCategory = $fetchResult['categories'][0];
            $updateData = [
                'cat_id' => $firstCategory['cat_id'],
                'cat_name' => $firstCategory['cat_name'] . ' (Updated)',
                'user_id' => $user_id
            ];
            
            $updateResult = $categoryController->update_category_ctr($updateData);
            echo "<p><strong>Update Result:</strong> " . json_encode($updateResult) . "</p>";
        }
        
        // Test 4: Test delete if we have categories
        if ($fetchResult['status'] === 'success' && !empty($fetchResult['categories'])) {
            echo "<h3>4. Testing Delete Category</h3>";
            $firstCategory = $fetchResult['categories'][0];
            $deleteData = [
                'cat_id' => $firstCategory['cat_id'],
                'user_id' => $user_id
            ];
            
            $deleteResult = $categoryController->delete_category_ctr($deleteData);
            echo "<p><strong>Delete Result:</strong> " . json_encode($deleteResult) . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error testing category system: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr><h2>Next Steps</h2>";
    echo "<ol>";
    echo "<li><a href='admin/category.php'>Go to Category Management Page</a></li>";
    echo "<li><a href='index.php'>Go to Homepage</a></li>";
    echo "<li>Test the CRUD operations in the web interface</li>";
    echo "</ol>";
    
} else {
    echo "<p>❌ User is not an admin. Cannot test category system.</p>";
}

echo "<hr>";
echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>
