<?php
require_once '../settings/core.php';

// Require admin access - redirect if not admin
if (!is_logged_in()) {
    header('Location: ../login/login.php?error=login_required');
    exit();
}

if (!is_admin()) {
    header('Location: ../index.php?error=admin_required');
    exit();
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    require_once '../controllers/product_controller.php';
    $productController = new ProductController();
    
    switch ($_POST['action']) {
        case 'fetch':
            $result = $productController->get_products_ctr(get_user_id());
            echo json_encode([
                'success' => $result['success'],
                'data' => $result['data'] ?? [],
                'count' => count($result['data'] ?? [])
            ]);
            break;
            
        case 'add':
            $product_name = trim($_POST['product_name'] ?? '');
            $product_description = trim($_POST['product_description'] ?? '');
            $product_price = floatval($_POST['product_price'] ?? 0);
            $product_stock = intval($_POST['product_stock'] ?? 0);
            $cat_id = intval($_POST['cat_id'] ?? 0);
            
            if (empty($product_name) || empty($product_description) || $product_price <= 0 || $product_stock < 0 || $cat_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'All fields are required and must be valid']);
                break;
            }
            
            $result = $productController->add_product_ctr([
                'product_name' => $product_name,
                'product_description' => $product_description,
                'product_price' => $product_price,
                'product_stock' => $product_stock,
                'cat_id' => $cat_id,
                'user_id' => get_user_id()
            ]);
            echo json_encode($result);
            break;
            
        case 'update':
            $product_id = intval($_POST['product_id'] ?? 0);
            $product_name = trim($_POST['product_name'] ?? '');
            $product_description = trim($_POST['product_description'] ?? '');
            $product_price = floatval($_POST['product_price'] ?? 0);
            $product_stock = intval($_POST['product_stock'] ?? 0);
            $cat_id = intval($_POST['cat_id'] ?? 0);
            
            if ($product_id <= 0 || empty($product_name) || empty($product_description) || $product_price <= 0 || $product_stock < 0 || $cat_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid data']);
                break;
            }
            
            $result = $productController->update_product_ctr([
                'product_id' => $product_id,
                'product_name' => $product_name,
                'product_description' => $product_description,
                'product_price' => $product_price,
                'product_stock' => $product_stock,
                'cat_id' => $cat_id,
                'user_id' => get_user_id()
            ]);
            echo json_encode($result);
            break;
            
        case 'delete':
            $product_id = intval($_POST['product_id'] ?? 0);
            if ($product_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
                break;
            }
            $result = $productController->delete_product_ctr($product_id, get_user_id());
            echo json_encode($result);
            break;
            
        case 'get_categories':
            require_once '../controllers/category_controller.php';
            $categoryController = new CategoryController();
            $result = $categoryController->get_categories_ctr(get_user_id());
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        * { font-family: 'Inter', sans-serif; }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
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
        
        .products-table {
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
        
        .price {
            font-weight: 600;
            color: #059669;
        }
        
        .stock {
            font-weight: 600;
        }
        
        .stock.low { color: #dc2626; }
        .stock.medium { color: #f59e0b; }
        .stock.high { color: #059669; }
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
            <h1><i class="fa fa-shopping-bag"></i> Product Management</h1>
            <p>Manage your products and inventory</p>
        </div>

        <!-- Alert Messages -->
        <div id="alertContainer"></div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="btn-action btn-add" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fa fa-plus"></i> Add New Product
            </button>
            <button class="btn-action btn-refresh" onclick="loadProducts()">
                <i class="fa fa-refresh"></i> Refresh
            </button>
            <a href="dashboard.php" class="btn-action btn-dashboard">
                <i class="fa fa-home"></i> Back to Dashboard
            </a>
        </div>

        <!-- Products Table -->
        <div class="products-table">
            <div class="table-header">
                <i class="fa fa-list"></i> Products List
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        <tr>
                            <td colspan="7" class="loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3">Loading products...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-plus"></i> Add New Product
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productName" class="form-label">Product Name *</label>
                                    <input type="text" class="form-control" id="productName" name="product_name" required maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productCategory" class="form-label">Category *</label>
                                    <select class="form-control" id="productCategory" name="cat_id" required>
                                        <option value="">Select Category</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Description *</label>
                            <textarea class="form-control" id="productDescription" name="product_description" rows="3" required maxlength="500"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productPrice" class="form-label">Price ($) *</label>
                                    <input type="number" class="form-control" id="productPrice" name="product_price" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productStock" class="form-label">Stock Quantity *</label>
                                    <input type="number" class="form-control" id="productStock" name="product_stock" min="0" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addProduct()">
                        <i class="fa fa-save"></i> Save Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-edit"></i> Edit Product
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        <input type="hidden" id="editProductId" name="product_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductName" class="form-label">Product Name *</label>
                                    <input type="text" class="form-control" id="editProductName" name="product_name" required maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductCategory" class="form-label">Category *</label>
                                    <select class="form-control" id="editProductCategory" name="cat_id" required>
                                        <option value="">Select Category</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editProductDescription" class="form-label">Description *</label>
                            <textarea class="form-control" id="editProductDescription" name="product_description" rows="3" required maxlength="500"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductPrice" class="form-label">Price ($) *</label>
                                    <input type="number" class="form-control" id="editProductPrice" name="product_price" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProductStock" class="form-label">Stock Quantity *</label>
                                    <input type="number" class="form-control" id="editProductStock" name="product_stock" min="0" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateProduct()">
                        <i class="fa fa-save"></i> Update Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Product Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-trash"></i> Delete Product
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this product?</p>
                    <div class="alert alert-danger">
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    <input type="hidden" id="deleteProductId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="deleteProduct()">
                        <i class="fa fa-trash"></i> Delete Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variables
        let products = [];
        let categories = [];

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCategories();
            loadProducts();
        });

        // Show alert message
        function showAlert(message, type = 'info') {
            const alertContainer = document.getElementById('alertContainer');
            const alertId = 'alert-' + Date.now();
            
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" id="${alertId}" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                const alert = document.getElementById(alertId);
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }

        // Load categories
        async function loadCategories() {
            try {
                const formData = new FormData();
                formData.append('action', 'get_categories');

                const response = await fetch('product_management.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    categories = result.data;
                    populateCategorySelects();
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        // Populate category selects
        function populateCategorySelects() {
            const selects = ['productCategory', 'editProductCategory'];
            selects.forEach(selectId => {
                const select = document.getElementById(selectId);
                select.innerHTML = '<option value="">Select Category</option>';
                categories.forEach(category => {
                    select.innerHTML += `<option value="${category.cat_id}">${escapeHtml(category.cat_name)}</option>`;
                });
            });
        }

        // Load products from server
        async function loadProducts() {
            try {
                const formData = new FormData();
                formData.append('action', 'fetch');

                const response = await fetch('product_management.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    products = result.data;
                    displayProducts();
                } else {
                    showAlert('Error loading products: ' + result.message, 'danger');
                }
            } catch (error) {
                console.error('Error loading products:', error);
                showAlert('Error loading products. Please try again.', 'danger');
            }
        }

        // Display products in table
        function displayProducts() {
            const tbody = document.getElementById('productsTableBody');
            
            if (products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No products found. Add your first product!</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = products.map(product => {
                const category = categories.find(cat => cat.cat_id == product.cat_id);
                const stockClass = product.product_stock <= 5 ? 'low' : product.product_stock <= 20 ? 'medium' : 'high';
                
                return `
                    <tr>
                        <td><strong>#${product.product_id}</strong></td>
                        <td>${escapeHtml(product.product_name)}</td>
                        <td>${escapeHtml(product.product_description.substring(0, 50))}${product.product_description.length > 50 ? '...' : ''}</td>
                        <td class="price">$${parseFloat(product.product_price).toFixed(2)}</td>
                        <td class="stock ${stockClass}">${product.product_stock}</td>
                        <td>${category ? escapeHtml(category.cat_name) : 'Unknown'}</td>
                        <td>
                            <button class="btn btn-edit me-2" onclick="editProduct(${product.product_id})">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-delete" onclick="confirmDelete(${product.product_id}, '${escapeHtml(product.product_name)}')">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Add new product
        async function addProduct() {
            const form = document.getElementById('addProductForm');
            const formData = new FormData(form);
            formData.append('action', 'add');

            try {
                const response = await fetch('product_management.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert('Product added successfully!', 'success');
                    form.reset();
                    bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
                    loadProducts();
                } else {
                    showAlert('Error adding product: ' + result.message, 'danger');
                }
            } catch (error) {
                console.error('Error adding product:', error);
                showAlert('Error adding product. Please try again.', 'danger');
            }
        }

        // Edit product
        function editProduct(productId) {
            const product = products.find(prod => prod.product_id == productId);
            if (!product) {
                showAlert('Product not found', 'danger');
                return;
            }

            document.getElementById('editProductId').value = product.product_id;
            document.getElementById('editProductName').value = product.product_name;
            document.getElementById('editProductDescription').value = product.product_description;
            document.getElementById('editProductPrice').value = product.product_price;
            document.getElementById('editProductStock').value = product.product_stock;
            document.getElementById('editProductCategory').value = product.cat_id;
            
            new bootstrap.Modal(document.getElementById('editProductModal')).show();
        }

        // Update product
        async function updateProduct() {
            const form = document.getElementById('editProductForm');
            const formData = new FormData(form);
            formData.append('action', 'update');

            try {
                const response = await fetch('product_management.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert('Product updated successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
                    loadProducts();
                } else {
                    showAlert('Error updating product: ' + result.message, 'danger');
                }
            } catch (error) {
                console.error('Error updating product:', error);
                showAlert('Error updating product. Please try again.', 'danger');
            }
        }

        // Confirm delete
        function confirmDelete(productId, productName) {
            document.getElementById('deleteProductId').value = productId;
            new bootstrap.Modal(document.getElementById('deleteProductModal')).show();
        }

        // Delete product
        async function deleteProduct() {
            const productId = document.getElementById('deleteProductId').value;

            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('product_id', productId);

                const response = await fetch('product_management.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    showAlert('Product deleted successfully!', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('deleteProductModal')).hide();
                    loadProducts();
                } else {
                    showAlert('Error deleting product: ' + result.message, 'danger');
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                showAlert('Error deleting product. Please try again.', 'danger');
            }
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
