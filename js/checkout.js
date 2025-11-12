// js/checkout.js

// Load cart items for checkout
function loadCheckoutCart() {
    $.ajax({
        url: 'actions/fetch_cart_action.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data && response.data.length > 0) {
                displayCheckoutItems(response.data, response.total);
            } else {
                // Redirect to cart if empty
                window.location.href = 'cart.php';
            }
        },
        error: function(xhr, status, error) {
            console.error('Checkout cart loading error:', error);
            showCheckoutNotification('Error loading cart', 'danger');
        }
    });
}

// Display checkout items
function displayCheckoutItems(items, total) {
    const container = $('#checkoutItemsContainer');
    let html = '<div class="checkout-items-list">';
    
    items.forEach(item => {
        const imagePath = item.product_image ? item.product_image : 'https://via.placeholder.com/100x100?text=No+Image';
        const subtotal = parseFloat(item.subtotal || (item.product_price * item.quantity)).toFixed(2);
        
        html += `
            <div class="checkout-item">
                <div class="checkout-item-image">
                    <img src="${imagePath}" alt="${item.product_title}" onerror="this.src='https://via.placeholder.com/100x100?text=No+Image'">
                </div>
                <div class="checkout-item-details">
                    <h6>${item.product_title}</h6>
                    <p class="text-muted small mb-1">
                        <i class="fa fa-tag"></i> ${item.cat_name || 'Uncategorized'} | 
                        <i class="fa fa-industry"></i> ${item.brand_name || 'No Brand'}
                    </p>
                    <p class="mb-0">
                        <strong>GHS ${parseFloat(item.product_price).toFixed(2)}</strong> x ${item.quantity} = 
                        <strong class="text-primary">GHS ${subtotal}</strong>
                    </p>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.html(html);
    
    // Update totals
    $('#checkoutSubtotal').text('GHS ' + total.toFixed(2));
    $('#checkoutTotal').text('GHS ' + total.toFixed(2));
    $('#checkoutItemCount').text(items.length);
}

// Process checkout
function processCheckout() {
    // Show payment modal
    $('#paymentModal').modal('show');
}

// Confirm payment
function confirmPayment() {
    // Disable button
    const btn = $('#confirmPaymentBtn');
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    
    $.ajax({
        url: 'actions/process_checkout_action.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                // Close payment modal
                $('#paymentModal').modal('hide');
                
                // Show success message
                showCheckoutSuccess(response);
            } else {
                btn.prop('disabled', false).html('<i class="fa fa-check"></i> Yes, I\'ve Paid');
                showCheckoutNotification(response.message || 'Checkout failed', 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Checkout error:', error);
            btn.prop('disabled', false).html('<i class="fa fa-check"></i> Yes, I\'ve Paid');
            showCheckoutNotification('Error processing checkout', 'danger');
        }
    });
}

// Show checkout success
function showCheckoutSuccess(response) {
    const orderRef = response.order_reference || 'N/A';
    const orderId = response.order_id || 'N/A';
    const totalAmount = response.total_amount || 0;
    
    const successHtml = `
        <div class="checkout-success-modal">
            <div class="success-content">
                <div class="success-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
                <h2>Order Placed Successfully!</h2>
                <p class="lead">Thank you for your purchase</p>
                <div class="order-details">
                    <div class="order-detail-item">
                        <strong>Order Reference:</strong>
                        <span class="order-ref">${orderRef}</span>
                    </div>
                    <div class="order-detail-item">
                        <strong>Order ID:</strong>
                        <span>${orderId}</span>
                    </div>
                    <div class="order-detail-item">
                        <strong>Total Amount:</strong>
                        <span class="order-total">GHS ${parseFloat(totalAmount).toFixed(2)}</span>
                    </div>
                </div>
                <div class="success-actions mt-4">
                    <a href="index.php" class="btn btn-primary btn-lg">
                        <i class="fa fa-home"></i> Go to Home
                    </a>
                    <a href="all_product.php" class="btn btn-outline-primary btn-lg">
                        <i class="fa fa-shopping-bag"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    `;
    
    // Replace checkout content with success message
    $('.checkout-container').html(successHtml);
    
    // Scroll to top
    $('html, body').animate({ scrollTop: 0 }, 500);
}

// Show notification
function showCheckoutNotification(message, type = 'info') {
    const notification = $(`
        <div class="alert alert-${type} alert-dismissible fade show notification-toast" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.fadeOut(() => notification.remove());
    }, 3000);
}

// Initialize checkout on page load
$(document).ready(function() {
    loadCheckoutCart();
    
    // Handle payment modal
    $('#paymentModal').on('hidden.bs.modal', function() {
        // Reset button if modal is closed without payment
        $('#confirmPaymentBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Yes, I\'ve Paid');
    });
});

