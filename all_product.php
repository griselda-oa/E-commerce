<?php
require_once 'settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Owusu Artisan Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .product-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .search-box {
            border-radius: 25px;
            border: 2px solid #007bff;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fa fa-shopping-bag"></i> Owusu Artisan Market
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link active" href="all_product.php">All Products</a>
                <?php if (is_logged_in()): ?>
                    <a class="nav-link" href="actions/logout_action.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login/login.php">Login</a>
                    <a class="nav-link" href="register/register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container" style="padding-top: 100px;">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">
                    <i class="fa fa-gem text-primary"></i> Authentic Ghanaian Artisan Products
                </h1>
                
                <!-- Search and Filter Section -->
                <div class="filter-section">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" id="searchInput" class="form-control search-box" placeholder="Search products...">
                        </div>
                        <div class="col-md-3">
                            <select id="categoryFilter" class="form-select">
                                <option value="">All Categories</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="brandFilter" class="form-select">
                                <option value="">All Brands</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button id="searchBtn" class="btn btn-primary w-100">
                                <i class="fa fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div id="productsContainer" class="row">
                    <!-- Products will be loaded here -->
                </div>

                <!-- Pagination -->
                <div id="pagination" class="text-center mt-4">
                    <!-- Pagination will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/all_product.js"></script>
</body>
</html>
