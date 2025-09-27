<?php
// simple_category_test.php - Simple test page for category management
session_start();
require_once 'settings/core.php';

// Set test session if not logged in
if (!isLoggedIn()) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 1;
    $_SESSION['user_name'] = 'Test Admin';
    $_SESSION['user_email'] = 'test@admin.com';
}

$user_id = currentUserId();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Category Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Simple Category Test</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Add Category</h5>
                    </div>
                    <div class="card-body">
                        <form id="addForm">
                            <div class="mb-3">
                                <label for="cat_name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="cat_name" name="cat_name" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Category</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Results</h5>
                    </div>
                    <div class="card-body">
                        <div id="results"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Current Categories</h5>
                    </div>
                    <div class="card-body">
                        <button id="loadBtn" class="btn btn-secondary">Load Categories</button>
                        <div id="categories" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Debug Info</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>User ID:</strong> <?php echo $user_id; ?></p>
                        <p><strong>Is Logged In:</strong> <?php echo isLoggedIn() ? 'Yes' : 'No'; ?></p>
                        <p><strong>Is Admin:</strong> <?php echo isAdmin() ? 'Yes' : 'No'; ?></p>
                        <p><strong>User Role:</strong> <?php echo $_SESSION['user_role'] ?? 'Not set'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add category form
        document.getElementById('addForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const resultsDiv = document.getElementById('results');
            
            resultsDiv.innerHTML = '<div class="alert alert-info">Adding category...</div>';
            
            try {
                console.log('=== ADD CATEGORY DEBUG ===');
                console.log('Form data:', Object.fromEntries(formData));
                
                const response = await fetch('actions/add_category_action.php', {
                    method: 'POST',
                    body: formData
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', [...response.headers.entries()]);
                
                const text = await response.text();
                console.log('Raw response:', text);
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    resultsDiv.innerHTML = `<div class="alert alert-danger">Invalid JSON response: ${text}</div>`;
                    return;
                }
                
                console.log('Parsed result:', result);
                
                if (result.status === 'success') {
                    resultsDiv.innerHTML = `<div class="alert alert-success">Success: ${result.message}</div>`;
                    this.reset();
                    loadCategories(); // Reload categories
                } else {
                    resultsDiv.innerHTML = `<div class="alert alert-danger">Error: ${result.message}</div>`;
                }
                
            } catch (error) {
                console.error('Error:', error);
                resultsDiv.innerHTML = `<div class="alert alert-danger">Network Error: ${error.message}</div>`;
            }
        });
        
        // Load categories
        async function loadCategories() {
            const categoriesDiv = document.getElementById('categories');
            
            try {
                console.log('=== LOAD CATEGORIES DEBUG ===');
                
                const response = await fetch('actions/fetch_category_action.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                
                console.log('Response status:', response.status);
                
                const text = await response.text();
                console.log('Raw response:', text);
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    categoriesDiv.innerHTML = `<div class="alert alert-danger">Invalid JSON response: ${text}</div>`;
                    return;
                }
                
                console.log('Parsed result:', result);
                
                if (result.status === 'success') {
                    if (result.categories && result.categories.length > 0) {
                        let html = '<table class="table table-striped"><thead><tr><th>ID</th><th>Name</th><th>User ID</th></tr></thead><tbody>';
                        result.categories.forEach(cat => {
                            html += `<tr><td>${cat.cat_id}</td><td>${cat.cat_name}</td><td>${cat.user_id}</td></tr>`;
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
        }
        
        // Load categories button
        document.getElementById('loadBtn').addEventListener('click', loadCategories);
        
        // Load categories on page load
        document.addEventListener('DOMContentLoaded', loadCategories);
    </script>
</body>
</html>
