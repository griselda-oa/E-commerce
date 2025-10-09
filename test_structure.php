<?php
// test_structure.php - Quick structure verification
echo "<h2>File Structure Test</h2>";
echo "<p>Current directory: " . __DIR__ . "</p>";

$files_to_check = [
    'controllers/customer_controller.php',
    'classes/customer_class.php',
    'settings/db_cred.php',
    'actions/register_customer_action.php'
];

echo "<h3>Checking Required Files:</h3>";
foreach ($files_to_check as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "<p style='color: green;'>✅ $file - FOUND</p>";
    } else {
        echo "<p style='color: red;'>❌ $file - MISSING</p>";
    }
}

echo "<h3>Directory Listing:</h3>";
echo "<pre>";
$dirs = ['actions', 'controllers', 'classes', 'settings', 'login', 'js'];
foreach ($dirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        echo "📁 $dir/\n";
        $files = scandir(__DIR__ . '/' . $dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "  📄 $file\n";
            }
        }
    } else {
        echo "❌ $dir/ - MISSING\n";
    }
}
echo "</pre>";
?>
