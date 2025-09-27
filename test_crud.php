<?php
// test_crud.php - Test CRUD functions after database fix
session_start();
require_once __DIR__ . '/classes/category_class.php';
require_once __DIR__ . '/controllers/category_controller.php';

echo "<h1>CRUD Functions Test</h1>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>❌ Please login first to test CRUD functions</p>";
    echo "<p><a href='login/login.php'>Go to Login</a></p>";
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';

echo "<p>✅ Logged in as: $user_name (ID: $user_id)</p>";

// Test Category Class
echo "<h2>Testing Category Class</h2>";
try {
    $category = new Category();
    echo "✅ Category class initialized<br>";
    
    // Test adding a category
    $test_cat_name = "Test Category " . date('H:i:s');
    $result = $category->add_category($test_cat_name, $user_id);
    
    if ($result !== false) {
        echo "✅ CREATE: Successfully added category '$test_cat_name' (ID: $result)<br>";
        $cat_id = $result;
        
        // Test reading categories
        $categories = $category->get_categories_by_user($user_id);
        if ($categories !== false) {
            echo "✅ READ: Successfully fetched " . count($categories) . " categories<br>";
            
            // Test updating category
            $new_name = "Updated Category " . date('H:i:s');
            $update_result = $category->update_category($cat_id, $new_name, $user_id);
            
            if ($update_result) {
                echo "✅ UPDATE: Successfully updated category to '$new_name'<br>";
                
                // Test getting single category
                $single_cat = $category->get_category_by_id($cat_id, $user_id);
                if ($single_cat) {
                    echo "✅ READ ONE: Successfully fetched category: " . $single_cat['cat_name'] . "<br>";
                    
                    // Test deleting category
                    $delete_result = $category->delete_category($cat_id, $user_id);
                    if ($delete_result) {
                        echo "✅ DELETE: Successfully deleted category<br>";
                    } else {
                        echo "❌ DELETE: Failed to delete category<br>";
                    }
                } else {
                    echo "❌ READ ONE: Failed to fetch single category<br>";
                }
            } else {
                echo "❌ UPDATE: Failed to update category<br>";
            }
        } else {
            echo "❌ READ: Failed to fetch categories<br>";
        }
    } else {
        echo "❌ CREATE: Failed to add category<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test Category Controller
echo "<h2>Testing Category Controller</h2>";
try {
    $controller = new CategoryController();
    echo "✅ Category controller initialized<br>";
    
    // Test controller methods
    $test_data = [
        'cat_name' => 'Controller Test ' . date('H:i:s'),
        'user_id' => $user_id
    ];
    
    $result = $controller->add_category_ctr($test_data);
    if ($result['status'] === 'success') {
        echo "✅ Controller CREATE: " . $result['message'] . "<br>";
        
        $fetch_result = $controller->fetch_categories_ctr(['user_id' => $user_id]);
        if ($fetch_result['status'] === 'success') {
            echo "✅ Controller READ: " . $fetch_result['message'] . "<br>";
            echo "Found " . $fetch_result['count'] . " categories<br>";
        } else {
            echo "❌ Controller READ: " . $fetch_result['message'] . "<br>";
        }
    } else {
        echo "❌ Controller CREATE: " . $result['message'] . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Controller Error: " . $e->getMessage() . "<br>";
}

echo "<h2>Test Summary</h2>";
echo "<p>If all tests show ✅, your CRUD functions are working correctly!</p>";
echo "<p><strong>Next:</strong> <a href='admin/category.php'>Go to Category Management</a></p>";

echo "<hr>";
echo "<p><em>CRUD test completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>

