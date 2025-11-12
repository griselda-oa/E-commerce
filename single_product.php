<?php
require_once 'settings/core.php';

$product_token = $_GET['token'] ?? '';
if (empty($product_token)) {
    header('Location: all_product.php');
    exit;
}

// Fetch product details by token
require_once 'controllers/product_controller.php';
$productController = new ProductController();
$result = $productController->get_product_by_token_ctr($product_token);

if (!$result['success']) {
    header('Location: all_product.php');
    exit;
}

$product = $result['data'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_title']); ?> - Owusu Artisan Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .product-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        .product-info {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
        }
        .price-display {
            font-size: 2.5rem;
            font-weight: bold;
            color: #007bff;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
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
                <a class="nav-link" href="all_product.php">All Products</a>
                <?php if (is_logged_in()): ?>
                    <a class="nav-link" href="actions/logout_action.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login/login.php">Login</a>
                    <a class="nav-link" href="login/register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container" style="padding-top: 100px;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="all_product.php">All Products</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['product_title']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Image -->
            <div class="col-md-6">
                <img src="<?php echo $product['product_image'] ? htmlspecialchars($product['product_image']) : 'https://via.placeholder.com/500x400?text=No+Image'; ?>" 
                     alt="<?php echo htmlspecialchars($product['product_title']); ?>" 
                     class="product-image">
            </div>

            <!-- Product Info -->
            <div class="col-md-6">
                <div class="product-info">
                    <h1 class="mb-3"><?php echo htmlspecialchars($product['product_title']); ?></h1>
                    
                    <div class="mb-3">
                        <span class="badge bg-primary me-2"><?php echo htmlspecialchars($product['cat_name']); ?></span>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($product['brand_name']); ?></span>
                    </div>

                    <div class="price-display mb-4">
                        GHS <?php echo number_format($product['product_price'], 2); ?>
                    </div>

                    <div class="mb-4">
                        <h5>Description</h5>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($product['product_description'])); ?></p>
                    </div>

                    <?php if (!empty($product['product_keyword'])): ?>
                    <div class="mb-4">
                        <h5>Keywords</h5>
                        <p class="text-muted"><?php echo htmlspecialchars($product['product_keyword']); ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <h5>Stock Status</h5>
                        <?php if (isset($product['product_stock']) && $product['product_stock'] > 0): ?>
                            <span class="badge bg-success">In Stock (<?php echo $product['product_stock']; ?> available)</span>
                        <?php else: ?>
                            <span class="badge bg-success">In Stock</span>
                        <?php endif; ?>
                        <!-- Note: product_stock column may not exist in actual database, handled gracefully -->
                    </div>

                    <div class="d-grid gap-2">
                        <?php if (is_logged_in()): ?>
                            <button class="btn btn-primary btn-lg" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                <i class="fa fa-shopping-cart"></i> Add to Cart
                            </button>
                        <?php else: ?>
                            <a href="login/login.php?redirect=<?php echo urlencode('single_product.php?token=' . $product_token); ?>" class="btn btn-primary btn-lg">
                                <i class="fa fa-sign-in-alt"></i> Login to Add to Cart
                            </a>
                        <?php endif; ?>
                        <a href="all_product.php" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Related Products</h3>
                <div id="relatedProducts" class="row">
                    <!-- Related products will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/cart.js"></script>
    <script>
        // addToCart function is defined in cart.js

        // Load related products
        $(document).ready(function() {
            $.ajax({
                url: 'actions/fetch_related_products_action.php',
                method: 'POST',
                data: { 
                    product_id: <?php echo $product['product_id']; ?>,
                    cat_id: <?php echo isset($product['product_cat']) ? $product['product_cat'] : ($product['cat_id'] ?? 0); ?>
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let html = '';
                        response.data.slice(0, 4).forEach(product => {
                            const imagePath = product.product_image ? product.product_image : 'https://via.placeholder.com/200x150?text=No+Image';
                            html += `
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100">
                                        <img src="${imagePath}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                        <div class="card-body">
                                            <h6 class="card-title">${product.product_title}</h6>
                                            <p class="text-primary">GHS ${parseFloat(product.product_price).toFixed(2)}</p>
                                            <a href="single_product.php?token=${product.product_token}" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#relatedProducts').html(html);
                    }
                }
            });
        });
    </script>
</body>
</html>
