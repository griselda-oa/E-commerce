<?php
// admin/product.php - Rebuilt Product Management Page
require_once '../settings/core.php';
require_once '../settings/security.php';
require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';

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

// Get categories and brands for dropdowns
$categoryController = new CategoryController();
$categories_result = $categoryController->get_categories_ctr();
$categories = $categories_result['success'] ? $categories_result['data'] : array();

$brandController = new BrandController();
$brands_result = $brandController->get_brands_ctr();
$brands = $brands_result['success'] ? $brands_result['data'] : array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .admin-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            margin: 20px auto;
            padding: 30px;
            max-width: 1400px;
        }
        .admin-header {
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .btn-action {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            margin-right: 10px;
            margin-bottom: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .product-image {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-image-placeholder {
            color: #6c757d;
            font-size: 4rem;
            opacity: 0.5;
        }
        .product-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .product-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
            line-height: 1.3;
        }
        .product-meta {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 12px;
        }
        .product-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: #28a745;
            margin-bottom: 15px;
        }
        .product-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: auto;
        }
        .product-actions .btn {
            flex: 1 1 auto;
            min-width: 80px;
            font-size: 0.75rem;
            padding: 8px 12px;
        }
        .loading-spinner {
            text-align: center;
            padding: 80px;
            color: #6c757d;
        }
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 5rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        .csv-upload-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fa fa-shopping-bag"></i> Product Management</h1>
            <p>Manage your e-commerce products</p>
        </div>
        
        <div class="alert alert-info" id="alertContainer" style="display: none;"></div>
        
        <!-- Action Buttons -->
        <div class="mb-3">
            <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fa fa-plus"></i> Add Product
            </button>
            <button class="btn btn-success btn-action" onclick="loadProducts()">
                <i class="fa fa-refresh"></i> Refresh
            </button>
            <button class="btn btn-info btn-action" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                <i class="fa fa-file-csv"></i> Bulk Upload (CSV)
            </button>
            <a href="brand.php" class="btn btn-warning btn-action"><i class="fa fa-industry"></i> Brands</a>
            <a href="category.php" class="btn btn-info btn-action"><i class="fa fa-tags"></i> Categories</a>
            <a href="../index.php" class="btn btn-secondary btn-action"><i class="fa fa-home"></i> Back to Dashboard</a>
        </div>
        
        <!-- Products Grid -->
        <div id="productsContainer" class="product-grid">
            <div class="text-center w-100">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Loading products...</p>
            </div>
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addProductModalLabel"><i class="fa fa-plus"></i> Add Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <input type="hidden" id="productId" name="product_id">
                        <input type="hidden" name="csrf_token" value="<?php echo SecurityManager::generateCSRFToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" id="categorySelect" name="cat_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?php echo $cat['cat_id']; ?>"><?php echo htmlspecialchars($cat['cat_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Brand *</label>
                                <select class="form-select" id="brandSelect" name="brand_id" required>
                                    <option value="">Select Brand</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Product Title *</label>
                            <input type="text" class="form-control" id="productTitle" name="product_title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Product Description *</label>
                            <textarea class="form-control" id="productDescription" name="product_description" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (GHS) *</label>
                                <input type="number" step="0.01" class="form-control" id="productPrice" name="product_price" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Keywords</label>
                                <input type="text" class="form-control" id="productKeywords" name="product_keyword" placeholder="e.g., smartphone, android, 5g">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="productImage" name="product_image" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()"><i class="fa fa-save"></i> Save Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk CSV Upload Modal -->
    <div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="bulkUploadModalLabel"><i class="fa fa-file-csv"></i> Bulk Upload Products (CSV)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6><i class="fa fa-info-circle"></i> CSV Format Instructions:</h6>
                        <p class="mb-2">Your CSV file should have the following columns (in order):</p>
                        <ol class="mb-0">
                            <li><strong>product_title</strong> - Product name</li>
                            <li><strong>product_description</strong> - Product description</li>
                            <li><strong>product_price</strong> - Price in GHS (e.g., 100.50)</li>
                            <li><strong>product_keyword</strong> - Keywords (comma-separated)</li>
                            <li><strong>cat_id</strong> - Category ID (must exist in database)</li>
                            <li><strong>brand_id</strong> - Brand ID (must exist in database)</li>
                        </ol>
                        <p class="mt-2 mb-0"><strong>Note:</strong> First row should be headers. Do not include product_id or product_image columns.</p>
                    </div>
                    <form id="bulkUploadForm">
                        <input type="hidden" name="csrf_token" value="<?php echo SecurityManager::generateCSRFToken(); ?>">
                        <div class="mb-3">
                            <label class="form-label">Select CSV File *</label>
                            <input type="file" class="form-control" id="csvFile" name="csv_file" accept=".csv" required>
                            <div class="form-text">Only CSV files are accepted. Max size: 10MB</div>
                        </div>
                        <div id="csvUploadStatus"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info" onclick="uploadBulkCSV()"><i class="fa fa-upload"></i> Upload CSV</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/product_admin.js"></script>
</body>
</html>
