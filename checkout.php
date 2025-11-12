<?php
require_once 'settings/core.php';

if (!is_logged_in()) {
    header('Location: login/login.php?redirect=checkout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Owusu Artisan Market</title>
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
        .checkout-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        .checkout-header {
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .checkout-header h1 {
            color: #2c3e50;
            font-weight: 800;
            margin: 0;
        }
        .checkout-item {
            display: flex;
            gap: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .checkout-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .checkout-item-image {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
        }
        .checkout-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .checkout-item-details {
            flex: 1;
        }
        .checkout-item-details h6 {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .checkout-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 30px;
            color: white;
            position: sticky;
            top: 100px;
        }
        .checkout-summary h3 {
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
        .btn-payment {
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
        .btn-payment:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            color: #667eea;
        }
        .checkout-success-modal {
            text-align: center;
            padding: 60px 20px;
        }
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease;
        }
        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        .order-details {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
        }
        .order-detail-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .order-detail-item:last-child {
            border-bottom: none;
        }
        .order-ref {
            font-family: monospace;
            font-size: 1.2rem;
            font-weight: 700;
            color: #667eea;
        }
        .order-total {
            font-size: 1.3rem;
            color: #28a745;
            font-weight: 700;
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
                <a class="nav-link" href="cart.php">
                    <i class="fa fa-shopping-cart"></i> Cart
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
            <!-- Checkout Items -->
            <div class="col-lg-8">
                <div class="checkout-container checkout-container">
                    <div class="checkout-header">
                        <h1><i class="fa fa-credit-card"></i> Checkout</h1>
                    </div>
                    <div id="checkoutItemsContainer">
                        <!-- Checkout items will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Checkout Summary -->
            <div class="col-lg-4">
                <div class="checkout-summary">
                    <h3><i class="fa fa-receipt"></i> Order Summary</h3>
                    <div class="summary-item">
                        <span>Items:</span>
                        <span id="checkoutItemCount">0</span>
                    </div>
                    <div class="summary-item">
                        <span>Subtotal:</span>
                        <span id="checkoutSubtotal">GHS 0.00</span>
                    </div>
                    <div class="summary-item">
                        <span>Total:</span>
                        <span id="checkoutTotal">GHS 0.00</span>
                    </div>
                    <button class="btn btn-payment" onclick="processCheckout()">
                        <i class="fa fa-credit-card"></i> Simulate Payment
                    </button>
                    <a href="cart.php" class="btn btn-outline-light btn-lg w-100 mt-3">
                        <i class="fa fa-arrow-left"></i> Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-credit-card"></i> Simulated Payment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-4">
                        <i class="fa fa-credit-card fa-4x text-primary mb-3"></i>
                        <h4>Simulated Payment</h4>
                        <p class="text-muted">This is a simulated payment process for demonstration purposes.</p>
                        <div class="alert alert-info">
                            <strong>Total Amount:</strong> <span id="modalTotal">GHS 0.00</span>
                        </div>
                    </div>
                    <p>Click "Yes, I've Paid" to complete the checkout process.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmPaymentBtn" onclick="confirmPayment()">
                        <i class="fa fa-check"></i> Yes, I've Paid
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/checkout.js"></script>
    <script>
        // Update modal total when cart loads
        $(document).ready(function() {
            setTimeout(function() {
                const total = $('#checkoutTotal').text();
                $('#modalTotal').text(total);
            }, 1000);
        });
    </script>
</body>
</html>

