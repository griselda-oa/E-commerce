<?php
// actions/test_db_connection.php
header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'Testing database connection...',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion(),
    'mysql_available' => extension_loaded('mysqli'),
    'error_reporting' => error_reporting(),
    'display_errors' => ini_get('display_errors')
]);

try {
    require_once __DIR__ . '/../settings/db_class.php';
    $db = new db_connection();
    
    // Test basic query
    $result = $db->query("SELECT 1 as test");
    if ($result) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'message' => 'Database connection successful!',
            'test_result' => $row['test'],
            'database_name' => $db->query("SELECT DATABASE() as db_name")->fetch_assoc()['db_name']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database query failed: ' . $db->error
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
