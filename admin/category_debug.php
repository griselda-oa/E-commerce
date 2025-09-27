<?php
// admin/category_debug.php - Debug version of category management
require_once __DIR__ . '/../settings/core.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../login/login.php');
    exit();
}

// Check if user is admin
if (!isAdmin()) {
    echo "<h1>Access Denied</h1>";
    echo "<p>You need to be an admin to access this page.</p>";
    echo "<p>Current user role: " . ($_SESSION['user_role'] ?? 'Not set') . "</p>";
    echo "<p><a href='../fix_admin_and_test.php'>Make yourself admin</a></p>";
    exit();
}

$user_id = currentUserId();
$user_name = $_SESSION['user_name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Category Management Debug - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Category Management Debug</h1>
        <p><strong>User ID:</strong> <?php echo $user_id; ?></p>
        <p><strong>User Name:</strong> <?php echo htmlspecialchars($user_name); ?></p>
        <p><strong>Is Admin:</strong> <?php echo isAdmin() ? 'Yes' : 'No'; ?></p>
        
        <hr>
        
        <!-- Simple Test Form -->
        <div class="card">
            <div class="card-header">
                <h5>Test Add Category</h5>
            </div>
            <div class="card-body">
                <form id="testForm">
                    <div class="mb-3">
                        <label for="cat_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="cat_name" name="cat_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </form>
            </div>
        </div>
        
        <!-- Results -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Results</h5>
            </div>
            <div class="card-body">
                <div id="results"></div>
            </div>
        </div>
        
        <!-- Categories List -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Current Categories</h5>
            </div>
            <div class="card-body">
                <button id="loadCategories" class="btn btn-secondary">Load Categories</button>
                <div id="categoriesList" class="mt-2"></div>
            </div>
        </div>
    </div>

    <script>
        // Test form submission
        document.getElementById('testForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const resultsDiv = document.getElementById('results');
            
            resultsDiv.innerHTML = '<div class="alert alert-info">Testing form submission...</div>';
            
            try {
                console.log('=== FORM SUBMISSION DEBUG ===');
                console.log('Form data:', Object.fromEntries(formData));
                console.log('Sending to: actions/add_category_action.php');
                
                const response = await fetch('actions/add_category_action.php', {
                    method: 'POST',
                    body: formData
                });
                
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('Response data:', result);
                
                if (result.status === 'success') {
                    resultsDiv.innerHTML = `<div class="alert alert-success">Success: ${result.message}</div>`;
                    // Clear form
                    this.reset();
                } else {
                    resultsDiv.innerHTML = `<div class="alert alert-danger">Error: ${result.message}</div>`;
                }
                
            } catch (error) {
                console.error('Error:', error);
                resultsDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
            }
        });
        
        // Load categories
        document.getElementById('loadCategories').addEventListener('click', async function() {
            const categoriesDiv = document.getElementById('categoriesList');
            
            categoriesDiv.innerHTML = '<div class="alert alert-info">Loading categories...</div>';
            
            try {
                console.log('=== LOAD CATEGORIES DEBUG ===');
                console.log('Sending to: actions/fetch_category_action.php');
                
                const response = await fetch('actions/fetch_category_action.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                console.log('Response data:', result);
                
                if (result.status === 'success') {
                    if (result.categories && result.categories.length > 0) {
                        let html = '<table class="table"><thead><tr><th>ID</th><th>Name</th></tr></thead><tbody>';
                        result.categories.forEach(cat => {
                            html += `<tr><td>${cat.cat_id}</td><td>${cat.cat_name}</td></tr>`;
                        });
                        html += '</tbody></table>';
                        categoriesDiv.innerHTML = html;
                    } else {
                        categoriesDiv.innerHTML = '<div class="alert alert-warning">No categories found</div>';
                    }
                } else {
                    categoriesDiv.innerHTML = `<div class="alert alert-danger">Error: ${result.message}</div>`;
                }
                
            } catch (error) {
                console.error('Error loading categories:', error);
                categoriesDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
            }
        });
    </script>
</body>
</html>
