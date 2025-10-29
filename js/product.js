// js/product.js
let products = [];
let currentAction = 'add'; // 'add' or 'edit'

$(document).ready(function() {
    loadProducts();
    loadAllBrands(); // Load all brands initially
    
    // Load brands when category changes
    $('#categorySelect').on('change', function() {
        loadBrandsForCategory($(this).val());
    });
    
    // Fix modal accessibility issues
    $('#addProductModal').on('shown.bs.modal', function() {
        $(this).removeAttr('aria-hidden');
    });
    
    $('#addProductModal').on('hidden.bs.modal', function() {
        $(this).attr('aria-hidden', 'true');
    });
    
    $('#uploadImageModal').on('shown.bs.modal', function() {
        $(this).removeAttr('aria-hidden');
    });
    
    $('#uploadImageModal').on('hidden.bs.modal', function() {
        $(this).attr('aria-hidden', 'true');
    });
});

function loadAllBrands() {
    console.log('Loading brands...');
    $.ajax({
        url: '../actions/fetch_brand_action.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            console.log('Brand response:', response);
            if (response.success && response.data) {
                let brandsHtml = '<option value="">Select Brand</option>';
                response.data.forEach(function(brand) {
                    brandsHtml += `<option value="${brand.brand_id}">${brand.brand_name}</option>`;
                });
                $('#brandSelect').html(brandsHtml);
                console.log('Brands loaded successfully:', response.data.length);
            } else {
                console.error('Brand loading failed:', response.message);
                $('#brandSelect').html('<option value="">No brands available</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Brand loading error:', error);
            $('#brandSelect').html('<option value="">Error loading brands</option>');
        }
    });
}

function loadBrandsForCategory(catId) {
    // For now, just load all brands since we removed cat_id from brands table
    loadAllBrands();
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
            <div class="empty-state">
                <i class="fa fa-shopping-bag"></i>
                <h4>No Products Yet</h4>
                <p>Start building your artisan marketplace by adding your first product!</p>
                <button class="btn btn-primary btn-lg mt-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fa fa-plus"></i> Add Your First Product
                </button>
            </div>
        `);
        return;
    }
    
    let html = '<div class="product-grid">';
    products.forEach(function(product) {
        const imagePath = product.product_image ? product.product_image : null;
        const imageHtml = imagePath ? 
            `<img src="${imagePath}" alt="${product.product_title}">` : 
            `<div class="product-image-placeholder"><i class="fa fa-image"></i></div>`;
            
        html += `
            <div class="product-card">
                <div class="product-image">
                    ${imageHtml}
                </div>
                <div class="product-content">
                    <h5 class="product-title">${product.product_title}</h5>
                    <div class="product-meta">
                        <i class="fa fa-tag"></i> ${product.cat_name || 'Uncategorized'} | 
                        <i class="fa fa-industry"></i> ${product.brand_name || 'No Brand'}
                    </div>
                    <div class="product-price">GHS ${parseFloat(product.product_price).toFixed(2)}</div>
                    <p class="text-muted small">${product.product_desc ? product.product_desc.substring(0, 100) + '...' : 'No description'}</p>
                    <div class="product-actions">
                        <button class="btn btn-warning btn-sm" onclick="editProduct(${product.product_id})">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-info btn-sm" onclick="uploadImage(${product.product_id})">
                            <i class="fa fa-image"></i> Upload Image
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.product_id})">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
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

function uploadImage(productId) {
    $('#uploadProductId').val(productId);
    $('#uploadImageModal').modal('show');
}

function saveImage() {
    const productId = $('#uploadProductId').val();
    const fileInput = document.getElementById('productImageFile');
    
    if (!fileInput.files[0]) {
        showAlert('Please select an image file', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('product_image', fileInput.files[0]);
    formData.append('product_id', productId);
    
    $.ajax({
        url: '../actions/upload_product_image_action.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showAlert('Image uploaded successfully!', 'success');
                $('#uploadImageModal').modal('hide');
                // Update the product image in database
                updateProductImage(productId, response.image_path);
            } else {
                showAlert('Error uploading image: ' + response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Upload error:', error);
            showAlert('Error uploading image. Please try again.', 'danger');
        }
    });
}

function updateProductImage(productId, imagePath) {
    $.ajax({
        url: '../actions/update_product_image_action.php',
        method: 'POST',
        data: {
            product_id: productId,
            product_image: imagePath
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                loadProducts(); // Reload products to show new image
            }
        },
        error: function(xhr, status, error) {
            console.error('Update error:', error);
        }
    });
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
