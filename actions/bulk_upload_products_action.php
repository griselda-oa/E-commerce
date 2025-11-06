<?php
// actions/bulk_upload_products_action.php
// Handle bulk product upload via CSV file
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/security.php';
require_once __DIR__ . '/../controllers/product_controller.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if user is admin
if (!is_admin()) {
    echo json_encode(['success' => false, 'message' => 'Admin privileges required']);
    exit;
}

// Check if CSV file was uploaded
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No CSV file uploaded or upload error']);
    exit;
}

// Validate file type
$file = $_FILES['csv_file'];
$file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($file_ext !== 'csv') {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only CSV files are allowed']);
    exit;
}

// Validate file size (max 10MB)
$max_size = 10 * 1024 * 1024; // 10MB
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds 10MB limit']);
    exit;
}

// Validate CSRF token
if (!SecurityManager::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

// Read CSV file
$csv_file = fopen($file['tmp_name'], 'r');
if (!$csv_file) {
    echo json_encode(['success' => false, 'message' => 'Failed to read CSV file']);
    exit;
}

// Read header row
$headers = fgetcsv($csv_file);
if (!$headers) {
    fclose($csv_file);
    echo json_encode(['success' => false, 'message' => 'CSV file is empty or invalid']);
    exit;
}

// Normalize headers (trim whitespace, lowercase)
$headers = array_map('trim', array_map('strtolower', $headers));

// Expected columns
$expected_columns = ['product_title', 'product_description', 'product_price', 'product_keyword', 'cat_id', 'brand_id'];
$column_indices = [];

// Find column indices
foreach ($expected_columns as $col) {
    $index = array_search($col, $headers);
    if ($index === false) {
        fclose($csv_file);
        echo json_encode([
            'success' => false, 
            'message' => "Missing required column: $col. Found columns: " . implode(', ', $headers)
        ]);
        exit;
    }
    $column_indices[$col] = $index;
}

// Initialize product controller
$productController = new ProductController();
$success_count = 0;
$error_count = 0;
$errors = [];
$line_number = 1; // Header is line 1

// Process each row
while (($row = fgetcsv($csv_file)) !== false) {
    $line_number++;
    
    // Skip empty rows
    if (empty(array_filter($row))) {
        continue;
    }
    
    // Extract data using column indices
    $product_data = [
        'product_title' => trim($row[$column_indices['product_title']] ?? ''),
        'product_description' => trim($row[$column_indices['product_description'] ?? ''] ?? ''),
        'product_price' => trim($row[$column_indices['product_price'] ?? ''] ?? '0'),
        'product_keyword' => trim($row[$column_indices['product_keyword'] ?? ''] ?? ''),
        'cat_id' => trim($row[$column_indices['cat_id'] ?? ''] ?? '0'),
        'brand_id' => trim($row[$column_indices['brand_id'] ?? ''] ?? '0'),
        'csrf_token' => SecurityManager::generateCSRFToken()
    ];
    
    // Validate required fields
    if (empty($product_data['product_title'])) {
        $errors[] = "Line $line_number: Product title is required";
        $error_count++;
        continue;
    }
    
    if (empty($product_data['product_description'])) {
        $errors[] = "Line $line_number: Product description is required";
        $error_count++;
        continue;
    }
    
    // Validate price
    $price = floatval($product_data['product_price']);
    if ($price <= 0) {
        $errors[] = "Line $line_number: Product price must be greater than 0";
        $error_count++;
        continue;
    }
    $product_data['product_price'] = $price;
    
    // Validate category ID
    $cat_id = intval($product_data['cat_id']);
    if ($cat_id <= 0) {
        $errors[] = "Line $line_number: Invalid category ID";
        $error_count++;
        continue;
    }
    $product_data['cat_id'] = $cat_id;
    
    // Validate brand ID
    $brand_id = intval($product_data['brand_id']);
    if ($brand_id <= 0) {
        $errors[] = "Line $line_number: Invalid brand ID";
        $error_count++;
        continue;
    }
    $product_data['brand_id'] = $brand_id;
    
    // Add product
    try {
        $result = $productController->add_product_ctr($product_data);
        if ($result['success']) {
            $success_count++;
        } else {
            $errors[] = "Line $line_number: " . ($result['message'] ?? 'Failed to add product');
            $error_count++;
        }
    } catch (Exception $e) {
        $errors[] = "Line $line_number: " . $e->getMessage();
        $error_count++;
    }
}

fclose($csv_file);

// Return results
$message = "Processed $line_number line(s). ";
if ($success_count > 0) {
    $message .= "$success_count product(s) added successfully. ";
}
if ($error_count > 0) {
    $message .= "$error_count error(s) occurred.";
}

echo json_encode([
    'success' => $success_count > 0,
    'message' => $message,
    'success_count' => $success_count,
    'error_count' => $error_count,
    'errors' => $errors
]);
?>

