<?php
// actions/update_category_action.php
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/category_controller.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to access this resource.'
    ]);
    exit();
}

// Check if user is admin
if (!isAdmin()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be an admin to access this resource.'
    ]);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Only POST requests are allowed.'
    ]);
    exit();
}

try {
    // Get current user ID
    $user_id = currentUserId();
    
    if (!$user_id) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unable to determine user ID.'
        ]);
        exit();
    }

    // Get and validate input data
    $cat_id = (int)($_POST['cat_id'] ?? 0);
    $cat_name = trim($_POST['cat_name'] ?? '');
    
    if ($cat_id <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Valid category ID is required.'
        ]);
        exit();
    }
    
    if (empty($cat_name)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Category name is required.'
        ]);
        exit();
    }

    // Create controller instance
    $categoryController = new CategoryController();
    
    // Prepare parameters
    $kwargs = [
        'cat_id' => $cat_id,
        'cat_name' => $cat_name,
        'user_id' => $user_id
    ];
    
    // Update category
    $result = $categoryController->update_category_ctr($kwargs);
    
    // Return JSON response
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log("Update category action error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An unexpected error occurred while updating the category.'
    ]);
}
