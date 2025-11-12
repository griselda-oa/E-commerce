<?php
require_once 'settings/core.php';

if (!is_logged_in()) {
    header('Location: login/login.php?redirect=cart.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Owusu Artisan Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding-top: 80px;
        }
        .navbar {
            background: rgba(102, 126, 234, 0.95) !important;
            backdrop-filter: blur(20px);
        }
        .cart-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        .cart-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .cart-header h1 {
            color: #2c3e50;
            font-weight: 800;
            margin: 0;
        }
        .cart-item-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .cart-item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        .cart-item-image {
            width: 150px;
            height: 150px;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
        }
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .cart-item-details {
            flex: 1;
        }
        .cart-item-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .cart-item-meta {
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        .cart-item-price {
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
        .price-label {
            color: #6c757d;
            margin-right: 10px;
        }
        .cart-item-quantity {
            margin-bottom: 15px;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        .quantity-input {
            width: 80px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 8px;
            font-weight: 600;
        }
        .cart-item-subtotal {
            font-size: 1.2rem;
            color: #28a745;
            font-weight: 700;
        }
        .cart-item-actions {
            display: flex;
            align-items: flex-start;
        }
        .cart-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 30px;
            color: white;
            position: sticky;
            top: 100px;
        }
        .cart-summary h3 {
            color: white;
            margin-bottom: 20px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .summary-item:last-child {
            border-bottom: none;
            font-size: 1.3rem;
            font-weight: 700;
        }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-cart i {
            font-size: 5rem;
            color: #6c757d;
            opacity: 0.5;
            margin-bottom: 20px;
        }
        .btn-checkout {
            background: white;
            color: #667eea;
            border: none;
            border-radius: 50px;
            padding: 15px 30px;
            font-weight: 700;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .btn-checkout:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            color: #667eea;
        }
        .btn-empty-cart {
            background: transparent;
            color: white;
            border: 2px solid white;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        .btn-empty-cart:hover {
            background: white;
            color: #667eea;
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
                <a class="nav-link active" href="cart.php">
                    <i class="fa fa-shopping-cart"></i> Cart
                    <span id="cartBadge" class="badge bg-danger ms-1" style="display: none;">0</span>
                </a>
                <?php if (is_logged_in()): ?>
                    <a class="nav-link" href="actions/logout_action.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login/login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="cart-container">
                    <div class="cart-header">
                        <h1><i class="fa fa-shopping-cart"></i> Shopping Cart</h1>
                    </div>
                    <div id="cartItemsContainer">
                        <!-- Cart items will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="cart-summary" id="cartSummary" style="display: none;">
                    <h3><i class="fa fa-receipt"></i> Order Summary</h3>
                    <div class="summary-item">
                        <span>Items:</span>
                        <span id="cartItemCount">0</span>
                    </div>
                    <div class="summary-item">
                        <span>Subtotal:</span>
                        <span id="cartSubtotal">GHS 0.00</span>
                    </div>
                    <div class="summary-item">
                        <span>Total:</span>
                        <span id="cartTotal">GHS 0.00</span>
                    </div>
                    <a href="checkout.php" class="btn btn-checkout">
                        <i class="fa fa-credit-card"></i> Proceed to Checkout
                    </a>
                    <button class="btn btn-empty-cart" onclick="emptyCart()">
                        <i class="fa fa-trash"></i> Empty Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/cart.js"></script>
    <script>
        // Update cart badge on page load
        $(document).ready(function() {
            if (typeof updateCartBadge === 'function') {
                updateCartBadge();
            }
        });
    </script>
</body>
</html>

