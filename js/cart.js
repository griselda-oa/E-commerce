// js/cart.js
let cartItems = [];
let cartTotal = 0;

// Load cart items
function loadCart() {
    $.ajax({
        url: 'actions/fetch_cart_action.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                cartItems = response.data || [];
                cartTotal = response.total || 0;
                displayCart();
                updateCartBadge();
            } else {
                cartItems = [];
                cartTotal = 0;
                displayCart();
                updateCartBadge();
            }
        },
        error: function(xhr, status, error) {
            console.error('Cart loading error:', error);
            showNotification('Error loading cart', 'danger');
            cartItems = [];
            displayCart();
        }
    });
}

// Display cart items
function displayCart() {
    const container = $('#cartItemsContainer');
    
    if (!cartItems || cartItems.length === 0) {
        container.html(`
            <div class="empty-cart text-center py-5">
                <i class="fa fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h4>Your cart is empty</h4>
                <p class="text-muted">Add some products to get started!</p>
                <a href="all_product.php" class="btn btn-primary mt-3">
                    <i class="fa fa-shopping-bag"></i> Browse Products
                </a>
            </div>
        `);
        $('#cartSummary').hide();
        return;
    }

    $('#cartSummary').show();
    let html = '<div class="cart-items-list">';
    
    cartItems.forEach(item => {
        const imagePath = item.product_image ? item.product_image : 'https://via.placeholder.com/150x150?text=No+Image';
        const subtotal = parseFloat(item.subtotal || (item.product_price * item.quantity)).toFixed(2);
        
        html += `
            <div class="cart-item-card" data-cart-id="${item.cart_id}">
                <div class="cart-item-image">
                    <img src="${imagePath}" alt="${item.product_title}" onerror="this.src='https://via.placeholder.com/150x150?text=No+Image'">
                </div>
                <div class="cart-item-details">
                    <h5 class="cart-item-title">${item.product_title}</h5>
                    <p class="cart-item-meta text-muted">
                        <i class="fa fa-tag"></i> ${item.cat_name || 'Uncategorized'} | 
                        <i class="fa fa-industry"></i> ${item.brand_name || 'No Brand'}
                    </p>
                    <div class="cart-item-price">
                        <span class="price-label">Price:</span> GHS ${parseFloat(item.product_price).toFixed(2)}
                    </div>
                    <div class="cart-item-quantity">
                        <label>Quantity:</label>
                        <div class="quantity-controls">
                            <button class="btn btn-sm btn-outline-secondary" onclick="decreaseQuantity(${item.cart_id}, ${item.quantity})">
                                <i class="fa fa-minus"></i>
                            </button>
                            <input type="number" class="quantity-input" value="${item.quantity}" 
                                   min="1" max="${item.product_stock || 999}" 
                                   onchange="updateQuantity(${item.cart_id}, this.value)">
                            <button class="btn btn-sm btn-outline-secondary" onclick="increaseQuantity(${item.cart_id}, ${item.quantity}, ${item.product_stock || 999})">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="cart-item-subtotal">
                        <strong>Subtotal: GHS ${subtotal}</strong>
                    </div>
                </div>
                <div class="cart-item-actions">
                    <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.cart_id})">
                        <i class="fa fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.html(html);
    
    // Update total
    $('#cartTotal').text('GHS ' + cartTotal.toFixed(2));
    $('#cartItemCount').text(cartItems.length);
}

// Add to cart
function addToCart(productId, quantity = 1) {
    $.ajax({
        url: 'actions/add_to_cart_action.php',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                showNotification(response.message || 'Product added to cart!', 'success');
                loadCart();
                updateCartBadge();
            } else {
                showNotification(response.message || 'Failed to add product to cart', 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Add to cart error:', error);
            showNotification('Error adding product to cart', 'danger');
        }
    });
}

// Remove from cart
function removeFromCart(cartId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }

    $.ajax({
        url: 'actions/remove_from_cart_action.php',
        method: 'POST',
        data: {
            cart_id: cartId
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                showNotification(response.message || 'Item removed from cart', 'success');
                loadCart();
                updateCartBadge();
            } else {
                showNotification(response.message || 'Failed to remove item', 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Remove from cart error:', error);
            showNotification('Error removing item from cart', 'danger');
        }
    });
}

// Update quantity
function updateQuantity(cartId, quantity) {
    quantity = parseInt(quantity);
    if (quantity <= 0) {
        quantity = 1;
    }

    $.ajax({
        url: 'actions/update_quantity_action.php',
        method: 'POST',
        data: {
            cart_id: cartId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                loadCart();
                updateCartBadge();
            } else {
                showNotification(response.message || 'Failed to update quantity', 'danger');
                loadCart();
            }
        },
        error: function(xhr, status, error) {
            console.error('Update quantity error:', error);
            showNotification('Error updating quantity', 'danger');
            loadCart();
        }
    });
}

// Increase quantity
function increaseQuantity(cartId, currentQty, maxStock) {
    const newQty = parseInt(currentQty) + 1;
    if (maxStock && newQty > maxStock) {
        showNotification('Cannot exceed available stock', 'warning');
        return;
    }
    updateQuantity(cartId, newQty);
}

// Decrease quantity
function decreaseQuantity(cartId, currentQty) {
    const newQty = parseInt(currentQty) - 1;
    if (newQty < 1) {
        removeFromCart(cartId);
    } else {
        updateQuantity(cartId, newQty);
    }
}

// Empty cart
function emptyCart() {
    if (!confirm('Are you sure you want to empty your cart? This action cannot be undone.')) {
        return;
    }

    $.ajax({
        url: 'actions/empty_cart_action.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                showNotification(response.message || 'Cart emptied successfully', 'success');
                loadCart();
                updateCartBadge();
            } else {
                showNotification(response.message || 'Failed to empty cart', 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Empty cart error:', error);
            showNotification('Error emptying cart', 'danger');
        }
    });
}

// Update cart badge in navbar
function updateCartBadge() {
    const badge = $('#cartBadge');
    if (badge.length) {
        if (cartItems && cartItems.length > 0) {
            badge.text(cartItems.length).show();
        } else {
            badge.hide();
        }
    }
}

// Show notification
function showNotification(message, type = 'info') {
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

// Initialize cart on page load
$(document).ready(function() {
    loadCart();
    
    // Auto-refresh cart every 30 seconds
    setInterval(loadCart, 30000);
});

