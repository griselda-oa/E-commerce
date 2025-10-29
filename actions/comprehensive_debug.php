<?php
// actions/comprehensive_debug.php
header('Content-Type: application/json');

echo json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'server_info' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
    'script_path' => __FILE__,
    'current_dir' => __DIR__
]);

echo "\n\n=== DATABASE CONNECTION TEST ===\n";

try {
    require_once __DIR__ . '/../settings/db_class.php';
    $db = new db_connection();
    
    echo "✅ Database connection successful\n";
    echo "Database name: " . $db->query("SELECT DATABASE() as db_name")->fetch_assoc()['db_name'] . "\n";
    
    // Test tables
    $tables = ['categories', 'brands', 'products'];
    foreach ($tables as $table) {
        $result = $db->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            $count = $db->query("SELECT COUNT(*) as count FROM $table")->fetch_assoc()['count'];
            echo "✅ Table '$table' exists with $count records\n";
        } else {
            echo "❌ Table '$table' does not exist\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n=== FILE EXISTENCE TEST ===\n";

$files_to_check = [
    '../settings/core.php',
    '../settings/db_class.php', 
    '../settings/db_cred.php',
    '../controllers/category_controller.php',
    '../classes/category_class.php',
    '../actions/fetch_category_action.php',
    '../actions/quick_fix_categories_action.php'
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        echo "✅ $file exists\n";
    } else {
        echo "❌ $file missing\n";
    }
}

echo "\n=== SESSION TEST ===\n";
session_start();
echo "Session ID: " . session_id() . "\n";
echo "Session data: " . json_encode($_SESSION) . "\n";

echo "\n=== DIRECTORY PERMISSIONS ===\n";
$dirs_to_check = [
    '../uploads',
    '../actions',
    '../classes',
    '../controllers'
];

foreach ($dirs_to_check as $dir) {
    $full_path = __DIR__ . '/' . $dir;
    if (is_dir($full_path)) {
        $perms = substr(sprintf('%o', fileperms($full_path)), -4);
        echo "✅ $dir exists (permissions: $perms)\n";
    } else {
        echo "❌ $dir missing\n";
    }
}
?>
