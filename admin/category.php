<?php
// admin/category.php
require_once __DIR__ . '/../settings/core.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../login/login.php');
    exit();
}

// Check if user is admin
if (!isAdmin()) {
    header('Location: ../login/login.php');
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
    <title>Category Management - E-Commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .navbar {
            background: #fff !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-bottom: 1px solid #e9ecef;
        }
        
        .navbar-brand {
            color: #D19C97 !important;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .navbar-brand:hover {
            color: #b77a7a !important;
        }
        
        .nav-link {
            color: #495057 !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .nav-link:hover {
            color: #D19C97 !important;
        }
        
        .nav-link.active {
            color: #D19C97 !important;
            font-weight: 600;
        }
        
        .btn-custom {
            background-color: #D19C97;
            border-color: #D19C97;
            color: #fff;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-custom:hover {
            background-color: #b77a7a;
            border-color: #b77a7a;
            color: #fff;
            transform: translateY(-1px);
        }
        
        .category-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #D19C97;
        }
        
        .card-header {
            background-color: #D19C97;
            color: white;
            border: none;
            font-weight: 600;
        }
        
        .table th {
            background-color: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 15px;
        }
        
        .table td {
            padding: 15px;
            border: none;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .btn-edit {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            margin: 0 2px;
        }
        
        .btn-edit:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        
        .btn-delete {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            margin: 0 2px;
        }
        
        .btn-delete:hover {
            background-color: #c82333;
            border-color: #c82333;
        }
        
        .modal-header {
            background-color: #D19C97;
            color: white;
            border: none;
        }
        
        .form-control:focus {
            border-color: #D19C97;
            box-shadow: 0 0 0 0.2rem rgba(209, 156, 151, 0.25);
        }
        
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .page-header {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .page-title {
            color: #2c3e50;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 8px;
        }
        
        .page-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        #noCategoriesMessage {
            text-align: center;
            padding: 60px 30px;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .page-title {
                font-size: 1.5rem;
            }
            
            .btn-custom {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">
                <i class="fa fa-shopping-bag text-primary"></i> E-Commerce Store
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fa fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="category.php">
                            <i class="fa fa-tags"></i> Categories
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fa fa-user-circle"></i> <?php echo htmlspecialchars($user_name); ?>
                            <span class="badge bg-warning ms-1"><i class="fa fa-crown"></i> Admin</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../index.php"><i class="fa fa-home"></i> Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../login/logout.php">
                                <i class="fa fa-sign-out-alt"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="page-title">
                                <i class="fa fa-tags"></i> Category Management
                            </h1>
                            <p class="page-subtitle">Manage your product categories</p>
                        </div>
                        <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fa fa-plus"></i> Add New Category
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <div id="alertContainer"></div>

        <!-- Categories Table -->
        <div class="row">
            <div class="col-12">
                <div class="card category-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa fa-list"></i> Your Categories
                            <span id="categoryCount" class="badge bg-primary ms-2">0</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Category Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="categoriesTableBody">
                                    <!-- Categories will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        <div id="noCategoriesMessage" class="text-center text-muted py-4" style="display: none;">
                            <i class="fa fa-tags fa-3x mb-3"></i>
                            <h5>No categories found</h5>
                            <p>Start by adding your first category!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">
                        <i class="fa fa-plus"></i> Add New Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addCategoryForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="addCategoryName" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="addCategoryName" name="cat_name" required maxlength="100" placeholder="Enter category name">
                            <div class="form-text">Category name must be unique and less than 100 characters.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-custom">
                            <i class="fa fa-plus"></i> Add Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">
                        <i class="fa fa-edit"></i> Edit Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCategoryForm">
                    <input type="hidden" id="editCategoryId" name="cat_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="editCategoryName" name="cat_name" required maxlength="100" placeholder="Enter category name">
                            <div class="form-text">Category name must be unique and less than 100 characters.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-custom">
                            <i class="fa fa-save"></i> Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteCategoryModalLabel">
                        <i class="fa fa-trash"></i> Delete Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this category?</p>
                    <div class="alert alert-warning">
                        <strong>Category:</strong> <span id="deleteCategoryName"></span>
                    </div>
                    <p class="text-danger"><strong>Warning:</strong> This action cannot be undone!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fa fa-trash"></i> Delete Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/category.js"></script>
    <script>
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Load categories on page load
            loadCategories();
        });
    </script>
</body>
</html>
