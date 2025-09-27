<?php
session_start();

echo "<h1>Final System Test</h1>";

if (!isset($_SESSION['user_id'])) {
    echo "<p>Please login first: <a href='login/login.php'>Login</a></p>";
    exit();
}

echo "<p>Logged in as: " . ($_SESSION['user_name'] ?? 'User') . "</p>";
echo "<p>Role: " . ($_SESSION['user_role'] ?? 'Unknown') . "</p>";

if ($_SESSION['user_role'] == 1) {
    echo "<p>✅ Admin user - CRUD functions available</p>";
    echo "<p><a href='admin/category.php'>Go to Category Management</a></p>";
} else {
    echo "<p>⚠️ Customer user - <a href='make_admin.php'>Make me admin</a></p>";
}

echo "<p><a href='index.php'>Back to Homepage</a></p>";
?>

