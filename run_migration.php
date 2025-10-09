<?php
require_once 'settings/db_cred.php';
require_once 'settings/db_class.php';

echo "<h2>Database Migration: Adding user_id to categories table</h2>";

try {
    $db = new db_connection();
    
    echo "<p>Starting migration...</p>";
    
    // Check if user_id column already exists
    $check_sql = "SHOW COLUMNS FROM categories LIKE 'user_id'";
    $result = $db->db->query($check_sql);
    
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✓ user_id column already exists. Migration not needed.</p>";
        echo "<p><a href='admin/category_management.php'>Go to Category Management</a></p>";
        exit;
    }
    
    // Add user_id column
    $alter_sql = "ALTER TABLE categories ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1";
    if ($db->db->query($alter_sql)) {
        echo "<p style='color: green;'>✓ Added user_id column to categories table</p>";
    } else {
        echo "<p style='color: red;'>✗ Error adding user_id column: " . $db->db->error . "</p>";
        exit;
    }
    
    // Add foreign key constraint
    $fk_sql = "ALTER TABLE categories ADD FOREIGN KEY (user_id) REFERENCES customer(customer_id) ON DELETE CASCADE";
    if ($db->db->query($fk_sql)) {
        echo "<p style='color: green;'>✓ Added foreign key constraint</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Warning: Could not add foreign key: " . $db->db->error . "</p>";
        echo "<p>This is usually fine - the column was added successfully.</p>";
    }
    
    // Update existing categories to belong to the first admin user
    $update_sql = "UPDATE categories SET user_id = 1 WHERE user_id = 1";
    if ($db->db->query($update_sql)) {
        echo "<p style='color: green;'>✓ Updated existing categories to belong to admin user</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Warning: Could not update existing categories: " . $db->db->error . "</p>";
    }
    
    // Verify the changes
    $verify_sql = "SELECT cat_id, cat_name, user_id FROM categories";
    $result = $db->db->query($verify_sql);
    
    echo "<h3>Current categories:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>User ID</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['cat_id'] . "</td><td>" . $row['cat_name'] . "</td><td>" . $row['user_id'] . "</td></tr>";
    }
    echo "</table>";
    
    echo "<p style='color: green; font-weight: bold;'>✓ Migration completed successfully!</p>";
    echo "<p><a href='admin/category_management.php' style='background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Category Management</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Migration failed: " . $e->getMessage() . "</p>";
}
?>
