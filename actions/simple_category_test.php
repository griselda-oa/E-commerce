<?php
// actions/simple_category_test.php
header('Content-Type: application/json');

echo json_encode([
    'test' => 'Starting simple category test...',
    'timestamp' => date('Y-m-d H:i:s')
]);

try {
    // Direct database connection
    require_once __DIR__ . '/../settings/db_cred.php';
    
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    if ($mysqli->connect_error) {
        echo json_encode([
            'success' => false,
            'error' => 'Connection failed: ' . $mysqli->connect_error
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Database connected successfully',
        'host' => DB_HOST,
        'database' => DB_NAME,
        'user' => DB_USER
    ]);
    
    // Test categories table
    $result = $mysqli->query("SELECT cat_id, cat_name FROM categories ORDER BY cat_name");
    
    if (!$result) {
        echo json_encode([
            'success' => false,
            'error' => 'Query failed: ' . $mysqli->error
        ]);
        exit;
    }
    
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $categories,
        'count' => count($categories),
        'message' => 'Categories loaded successfully'
    ]);
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Exception: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
