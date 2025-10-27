<?php
// Test database connection and categories loading
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test for Server</h2>";

// Test 1: Check database connection
require_once 'settings/db_cred.php';

// Add timeout to prevent hanging
ini_set('default_socket_timeout', 5);

echo "<h3>Database Configuration:</h3>";
echo "<p>DB_HOST: " . DB_HOST . "</p>";
echo "<p>DB_PORT: " . DB_PORT . "</p>";
echo "<p>DB_NAME: " . DB_NAME . "</p>";
echo "<p>DB_USER: " . DB_USER . "</p>";
echo "<p>DB_PASS: " . (DB_PASS ? '***SET***' : 'EMPTY') . "</p>";

// Test 2: Connect to database
$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if ($mysqli->connect_error) {
    echo "<p style='color:red;'>Connection FAILED: " . $mysqli->connect_error . "</p>";
} else {
    echo "<p style='color:green;'>✅ Database connection successful!</p>";
    
    // Test 3: Check if categories table exists
    $result = $mysqli->query("SHOW TABLES LIKE 'categories'");
    if ($result && $result->num_rows > 0) {
        echo "<p style='color:green;'>✅ Categories table exists</p>";
        
        // Test 4: Try to fetch categories
        $test_user_id = 2;
        $sql = "SELECT * FROM categories WHERE user_id = ?";
        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $test_user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            echo "<p style='color:green;'>✅ Query executed successfully</p>";
            echo "<p>Categories found: " . $result->num_rows . "</p>";
            
            if ($result->num_rows > 0) {
                echo "<h4>Categories:</h4>";
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($row['cat_name']) . " (ID: " . $row['cat_id'] . ")</li>";
                }
                echo "</ul>";
            } else {
                echo "<p style='color:orange;'>⚠️ No categories found for user ID: " . $test_user_id . "</p>";
                echo "<p>This might be why categories aren't loading. Check if database has sample data.</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Failed to prepare statement: " . $mysqli->error . "</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Categories table does NOT exist</p>";
        echo "<p>You need to import the database from db/dbforlab.sql</p>";
    }
    
    $mysqli->close();
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If database connection failed, check db_cred.php settings</li>";
echo "<li>If table doesn't exist, import db/dbforlab.sql into your database</li>";
echo "<li>If no categories found, check if sample data was imported correctly</li>";
echo "</ol>";
?>
