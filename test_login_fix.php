<?php
// test_login_fix.php - Test the login fix
session_start();

echo "<h1>Login Fix Test</h1>";

// Test 1: Check current session status
echo "<h2>Current Session Status:</h2>";
if (isset($_SESSION['user_id'])) {
    echo "✅ User is logged in<br>";
    echo "User ID: " . $_SESSION['user_id'] . "<br>";
    echo "User Name: " . ($_SESSION['user_name'] ?? 'N/A') . "<br>";
    echo "User Role: " . ($_SESSION['user_role'] ?? 'N/A') . "<br>";
    echo "<a href='login/logout.php'>Logout</a><br>";
} else {
    echo "❌ User is not logged in<br>";
    echo "<a href='login/login.php'>Go to Login</a><br>";
}

// Test 2: Check if login redirect works
echo "<h2>Login Redirect Test:</h2>";
if (isset($_SESSION['user_id'])) {
    echo "If you're logged in and visit login.php, you should be redirected to index.php<br>";
    echo "<a href='login/login.php' target='_blank'>Test Login Redirect</a><br>";
} else {
    echo "You need to be logged in to test the redirect<br>";
}

echo "<hr>";
echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>If you see 'User is not logged in', click 'Go to Login'</li>";
echo "<li>Login with: george.orwell@ashesi.edu.gh (admin user)</li>";
echo "<li>After login, try clicking 'Test Login Redirect' - it should redirect you back to index.php</li>";
echo "<li>No more 'You are already logged in' error!</li>";
echo "</ol>";
?>

