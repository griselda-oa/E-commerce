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
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .filter-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 40px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        .search-box {
            border-radius: 50px;
            border: 2px solid #667eea;
            padding: 15px 25px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        .search-box:focus {
            border-color: #764ba2;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
        }
        .form-select {
            border-radius: 15px;
            border: 2px solid #e9ecef;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 15px rgba(102, 126, 234, 0.2);
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
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
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        .product-image-placeholder {
            color: #6c757d;
            font-size: 4rem;
            opacity: 0.5;
        }
        .product-content {
            padding: 25px;
        }
        .product-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        .product-meta {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        .product-price {
            font-size: 1.6rem;
            font-weight: 800;
            color: #28a745;
            margin-bottom: 20px;
        }
        .product-description {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .btn-add-cart {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-add-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            color: white;
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
            margin-bottom: 30px;
            opacity: 0.5;
        }
        .navbar {
            background: rgba(102, 126, 234, 0.95) !important;
            backdrop-filter: blur(20px);
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
                    <a class="nav-link" href="cart.php">
                        <i class="fa fa-shopping-cart"></i> Cart
                        <span id="cartBadge" class="badge bg-danger ms-1" style="display: none;">0</span>
                    </a>
                    <a class="nav-link" href="actions/logout_action.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login/login.php">Login</a>
                    <a class="nav-link" href="login/register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1>
                <i class="fa fa-gem"></i> Authentic Ghanaian Artisan Products
            </h1>
            <p class="lead">Discover unique handcrafted treasures from Ghana's finest artisans</p>
        </div>
    </div>

    <div class="container">
        <!-- Search and Filter Section -->
        <div class="filter-section">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control search-box" placeholder="Search by keyword, title, description...">
                </div>
                <div class="col-md-2">
                    <select id="categoryFilter" class="form-select">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="brandFilter" class="form-select">
                        <option value="">All Brands</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" id="minPrice" class="form-control form-select" placeholder="Min Price (GHS)" step="0.01" min="0">
                </div>
                <div class="col-md-2">
                    <input type="number" id="maxPrice" class="form-control form-select" placeholder="Max Price (GHS)" step="0.01" min="0">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <button id="searchBtn" class="btn btn-primary btn-lg px-5">
                        <i class="fa fa-search"></i> Search Products
                    </button>
                    <button id="clearBtn" class="btn btn-outline-secondary btn-lg px-5 ms-2">
                        <i class="fa fa-times"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div id="productsContainer">
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
    <script src="js/cart.js"></script>
    <script src="js/all_product.js"></script>
</body>
</html>
