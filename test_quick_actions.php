<?php
// test_quick_actions.php - Test if Quick Actions CRUD button is visible
session_start();

echo "<h1>Quick Actions CRUD Button Test</h1>";

// Check session status
if (!isset($_SESSION['user_id'])) {
    echo "<p>❌ Not logged in. Please login first.</p>";
    echo "<p><a href='login/login.php'>Go to Login</a></p>";
    exit();
}

echo "<p>✅ Logged in as: " . ($_SESSION['user_name'] ?? 'User') . "</p>";
echo "<p>✅ User ID: " . $_SESSION['user_id'] . "</p>";
echo "<p>✅ User Role: " . $_SESSION['user_role'] . "</p>";

// Check if user is admin
if ($_SESSION['user_role'] == 1) {
    echo "<p>✅ User is ADMIN - Quick Actions CRUD button should be visible!</p>";
    echo "<p><strong>Expected Button:</strong> 'Manage Categories' with CRUD badge</p>";
    echo "<p><strong>Location:</strong> Quick Actions card on homepage</p>";
    echo "<p><strong>Direct Link:</strong> <a href='admin/category.php'>admin/category.php</a></p>";
} else {
    echo "<p>❌ User is NOT admin (role: " . $_SESSION['user_role'] . ") - CRUD button will not be visible</p>";
    echo "<p>To see the CRUD button, you need to be logged in as an admin user.</p>";
}

echo "<hr>";
echo "<h2>Quick Test Instructions:</h2>";
echo "<ol>";
echo "<li>Go to: <a href='index.php'>Homepage</a></li>";
echo "<li>Look for 'Quick Actions' card</li>";
echo "<li>You should see a purple 'Manage Categories' button with a red 'CRUD' badge</li>";
echo "<li>Click it to access category management</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>If Button is Not Visible:</h2>";
echo "<ol>";
echo "<li>Make sure you're logged in as admin (role = 1)</li>";
echo "<li>Check that the database has been updated: <a href='fix_database.php'>Fix Database</a></li>";
echo "<li>Clear browser cache and refresh the page</li>";
echo "</ol>";

echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>

