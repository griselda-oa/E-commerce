<?php
// make_admin.php - Make current user an admin
session_start();
require_once __DIR__ . '/settings/db_class.php';

echo "<h1>Make User Admin</h1>";

if (!isset($_SESSION['user_id'])) {
    echo "<p>❌ Please login first</p>";
    echo "<p><a href='login/login.php'>Go to Login</a></p>";
    exit();
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'] ?? '';

echo "<p>Current user: " . ($_SESSION['user_name'] ?? 'User') . " (ID: $user_id)</p>";
echo "<p>Current role: " . ($_SESSION['user_role'] ?? 'Unknown') . "</p>";

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
            echo "<p>The 'Manage Categories' button should now be visible in Quick Actions.</p>";
            
            echo "<hr>";
            echo "<h2>Next Steps:</h2>";
            echo "<ol>";
            echo "<li><a href='index.php'>Go to Homepage</a> - You should now see the CRUD button</li>";
            echo "<li><a href='admin/category.php'>Go to Category Management</a></li>";
            echo "<li>Test the CRUD operations</li>";
            echo "</ol>";
            
        } else {
            echo "<p>⚠️ No rows were updated. User might already be admin.</p>";
        }
    } else {
        echo "<p>❌ Failed to update user role: " . $stmt->error . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><em>Admin update completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>

