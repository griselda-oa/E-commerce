<?php
// Database connection test
require_once 'settings/db_cred.php';

echo "Testing database connection...\n";
echo "Server: " . SERVER . "\n";
echo "Database: " . DATABASE . "\n";
echo "Port: " . PORT . "\n";
echo "Username: " . USERNAME . "\n";

$mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE, PORT);

if ($mysqli->connect_errno) {
    echo "Connection failed: " . $mysqli->connect_error . "\n";
    echo "Error number: " . $mysqli->connect_errno . "\n";
} else {
    echo "Connection successful!\n";
    
    // Test if customer table exists
    $result = $mysqli->query("SHOW TABLES LIKE 'customer'");
    if ($result && $result->num_rows > 0) {
        echo "Customer table exists!\n";
        
        // Show table structure
        $result = $mysqli->query("DESCRIBE customer");
        if ($result) {
            echo "Customer table structure:\n";
            while ($row = $result->fetch_assoc()) {
                echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
            }
        }
    } else {
        echo "Customer table does not exist!\n";
    }
}

$mysqli->close();
?>
