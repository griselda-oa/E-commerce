// js/product.js
let products = [];
let currentAction = 'add'; // 'add' or 'edit'

$(document).ready(function() {
    loadProducts();
    
    // Load brands when category changes
    $('#categorySelect').on('change', function() {
        loadBrandsForCategory($(this).val());
    });
});

function loadBrandsForCategory(catId) {
    if (!catId) {
        $('#brandSelect').html('<option value="">Select Brand</option>');
        return;
    }
    
    $.ajax({
        url: '../actions/fetch_brand_action.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                let brandsHtml = '<option value="">Select Brand</option>';
                response.data.forEach(brand => {
                    if (brand.cat_id == catId) {
                        brandsHtml += `<option value="${brand.brand_id}">${brand.brand_name}</option>`;
                    }
                });
                $('#brandSelect').html(brandsHtml);
            }
        }
    });
}

function loadProducts() {
    $.ajax({
        url: '../actions/fetch_product_action.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                products = response.data;
                displayProducts();
            } else {
                showAlert('Error loading products: ' + response.message, 'danger');
            }
        }
    });
}

function displayProducts() {
    const container = $('#productsContainer');
    
    if (products.length === 0) {
        container.html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No products found. Add your first product!</p>
            </div>
        `);
        return;
    }
    
    let html = '';
    products.forEach(product => {
        const imagePath = product.product_image ? '../' + product.product_image : 'https://via.placeholder.com/200x200?text=No+Image';
        html += `
            <div class="product-card">
                <img src="${imagePath}" alt="${product.product_title}" class="img-fluid rounded mb-3" style="max-height: 200px; width: 100%; object-fit: cover;">
                <h5>${product.product_title}</h5>
                <p class="text-muted">${product.cat_name} - ${product.brand_name}</p>
                <p class="h4 text-primary">GHS ${parseFloat(product.product_price).toFixed(2)}</p>
                <p class="text-truncate">${product.product_description}</p>
                <div class="mt-3">
                    <button class="btn btn-sm btn-warning" onclick="editProduct(${product.product_id})">
                        <i class="fa fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.product_id})">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function saveProduct() {
    const productId = $('#productId').val();
    const formData = new FormData($('#productForm')[0]);
    
    // Validation
    if (!$('#productTitle').val() || !$('#productDescription').val() || !$('#productPrice').val()) {
        showAlert('Please fill in all required fields', 'warning');
        return;
    }
    
    let url = '../actions/add_product_action.php';
    if (productId) {
        url = '../actions/update_product_action.php';
    }
    
    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showAlert(response.message, 'success');
                $('#addProductModal').modal('hide');
                $('#productForm')[0].reset();
                loadProducts();
            } else {
                showAlert(response.message, 'danger');
            }
        }
    });
}

function editProduct(productId) {
    const product = products.find(p => p.product_id == productId);
    if (!product) return;
    
    $('#productId').val(product.product_id);
    $('#productTitle').val(product.product_title);
    $('#productDescription').val(product.product_description);
    $('#productPrice').val(product.product_price);
    $('#productKeywords').val(product.product_keyword);
    $('#categorySelect').val(product.cat_id);
    
    loadBrandsForCategory(product.cat_id);
    setTimeout(() => {
        $('#brandSelect').val(product.brand_id);
    }, 500);
    
    $('#addProductModal').modal('show');
}

function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product?')) return;
    
    // Implement delete functionality if needed
    showAlert('Delete functionality not implemented yet', 'info');
}

function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('#alertContainer').html(alertHtml);
    setTimeout(() => {
        $('#alertContainer').html('');
    }, 3000);
}
