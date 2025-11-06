<?php
// admin/category.php
require_once '../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    $login_url = get_absolute_path('login/login.php') . '?error=login_required';
    header('Location: ' . $login_url);
    exit();
}

// Check if user is admin
if (!is_admin()) {
    header('Location: ../index.php?error=admin_required');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admin-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin: 20px;
            padding: 30px;
            min-height: calc(100vh - 40px);
        }
        
        .admin-header {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .admin-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .back-to-home {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .btn-action {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .btn-add { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .btn-refresh { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; }
        .btn-dashboard { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
        
        .categories-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .table-header {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            color: white;
            padding: 20px;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .table th {
            background: #f8fafc;
            border: none;
            padding: 15px;
            font-weight: 600;
            color: #374151;
        }
        
        .table td {
            border: none;
            padding: 15px;
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background: #f8fafc;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .btn-edit:hover, .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        }
        
        .loading {
            text-align: center;
            padding: 40px;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
            border: 4px solid rgba(79, 70, 229, 0.1);
            border-left: 4px solid #4f46e5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading p {
            margin-top: 20px;
            color: #6b7280;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Back to Home Button -->
    <a href="../index.php" class="btn btn-light back-to-home">
        <i class="fa fa-arrow-left"></i> Back to Home
    </a>

    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <h1><i class="fa fa-tags"></i> Category Management</h1>
            <p>Manage your product categories</p>
        </div>

        <!-- Alert Messages -->
        <div id="alertContainer"></div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="btn-action btn-add" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fa fa-plus"></i> Add New Category
            </button>
            <button class="btn-action btn-refresh" onclick="loadCategories()">
                <i class="fa fa-refresh"></i> Refresh
            </button>
            <a href="../index.php" class="btn-action btn-dashboard">
                <i class="fa fa-home"></i> Back to Dashboard
            </a>
        </div>

        <!-- Categories Table -->
        <div class="categories-table">
            <div class="table-header">
                <i class="fa fa-list"></i> Categories List
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTableBody">
                        <tr>
                            <td colspan="4" class="loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3">Loading categories...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-plus"></i> Add New Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="categoryName" name="category_name" required maxlength="100">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addCategory()">
                        <i class="fa fa-save"></i> Save Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-edit"></i> Edit Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <input type="hidden" id="editCategoryId" name="category_id">
                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="editCategoryName" name="category_name" required maxlength="100">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateCategory()">
                        <i class="fa fa-save"></i> Update Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Category Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-trash"></i> Delete Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this category?</p>
                    <div class="alert alert-danger">
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    <input type="hidden" id="deleteCategoryId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="deleteCategory()">
                        <i class="fa fa-trash"></i> Delete Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/category.js"></script>
</body>
</html>
