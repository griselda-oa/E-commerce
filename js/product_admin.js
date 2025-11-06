// product_admin.js - Rebuilt Product Admin JavaScript
let products = [];

$(document).ready(function() {
    loadProducts();
    loadAllBrands();
    
    // Load brands when category changes
    $('#categorySelect').change(function() {
        loadBrandsForCategory($(this).val());
    });
    
    // Reset form when modal closes
    $('#addProductModal').on('hidden.bs.modal', function() {
        $('#productForm')[0].reset();
        $('#productId').val('');
        $('#existingProductImage').remove();
        $('#addProductModalLabel').html('<i class="fa fa-plus"></i> Add Product');
    });
});

// Load all products using fetch_all_products_action.php (which works)
function loadProducts() {
    const container = $('#productsContainer');
    container.html('<div class="text-center w-100"><div class="spinner-border text-primary" role="status"></div><p class="mt-3">Loading products...</p></div>');
    
    $.ajax({
        url: '../actions/fetch_all_products_action.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                products = response.data || [];
                displayProducts();
            } else {
                const errorMsg = response ? response.message : 'Unknown error';
                container.html('<div class="alert alert-danger w-100">Error loading products: ' + errorMsg + '</div>');
                showAlert('Error loading products: ' + errorMsg, 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Product loading error:', error, xhr.responseText);
            let errorMsg = 'Error connecting to server';
            if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    errorMsg = 'Server error: ' + xhr.status + ' ' + error;
                }
            }
            container.html('<div class="alert alert-danger w-100">' + errorMsg + '<br><button class="btn btn-sm btn-primary mt-2" onclick="loadProducts()">Retry</button></div>');
            showAlert(errorMsg, 'danger');
        }
    });
}

// Display products in grid
function displayProducts() {
    const container = $('#productsContainer');
    
    if (products.length === 0) {
        container.html(`
            <div class="empty-state w-100">
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
    
    let html = '';
    products.forEach(function(product) {
        const imagePath = product.product_image ? '../' + product.product_image : null;
        const imageHtml = imagePath ? 
            `<img src="${imagePath}" alt="${product.product_title}" onerror="this.parentElement.innerHTML='<div class=\\'product-image-placeholder\\'><i class=\\'fa fa-image\\'></i></div>';">` : 
            `<div class="product-image-placeholder"><i class="fa fa-image"></i></div>`;
        
        html += `
            <div class="product-card">
                <div class="product-image">
                    ${imageHtml}
                </div>
                <div class="product-content">
                    <h5 class="product-title">${product.product_title || 'Untitled Product'}</h5>
                    <div class="product-meta">
                        <i class="fa fa-tag"></i> ${product.cat_name || 'Uncategorized'} | 
                        <i class="fa fa-industry"></i> ${product.brand_name || 'No Brand'}
                    </div>
                    <div class="product-price">GHS ${parseFloat(product.product_price || 0).toFixed(2)}</div>
                    <p class="text-muted small">${(product.product_desc || product.product_description || '').substring(0, 100)}${(product.product_desc || product.product_description || '').length > 100 ? '...' : ''}</p>
                    <div class="product-actions">
                        <button class="btn btn-primary btn-sm" onclick="editProduct(${product.product_id})">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.product_id})">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

// Load all brands
function loadAllBrands() {
    $.ajax({
        url: '../actions/fetch_all_brands_action.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                const select = $('#brandSelect');
                select.html('<option value="">Select Brand</option>');
                response.data.forEach(function(brand) {
                    select.append(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
                });
            }
        }
    });
}

// Load brands for category
function loadBrandsForCategory(catId) {
    loadAllBrands(); // For now, just load all brands
}

// Save product
function saveProduct() {
    const form = $('#productForm')[0];
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = new FormData(form);
    const productId = $('#productId').val();
    const url = productId ? '../actions/update_product_action.php' : '../actions/add_product_action.php';
    
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
                loadProducts();
            } else {
                showAlert(response.message || 'Error saving product', 'danger');
            }
        },
        error: function(xhr, status, error) {
            showAlert('Error: ' + error, 'danger');
        }
    });
}

// Edit product
function editProduct(productId) {
    const product = products.find(p => p.product_id == productId);
    if (!product) {
        showAlert('Product not found', 'danger');
        return;
    }
    
    $('#productId').val(product.product_id);
    $('#productTitle').val(product.product_title);
    $('#productDescription').val(product.product_description || product.product_desc);
    $('#productPrice').val(product.product_price);
    $('#productKeywords').val(product.product_keyword || product.product_keywords);
    $('#categorySelect').val(product.cat_id);
    
    // Store existing image path for update
    if (product.product_image) {
        // Remove existing hidden input if present
        $('#existingProductImage').remove();
        $('<input>').attr({
            type: 'hidden',
            id: 'existingProductImage',
            name: 'existing_product_image',
            value: product.product_image
        }).appendTo('#productForm');
    }
    
    // Load brands and set selected brand
    loadAllBrands();
    setTimeout(() => {
        $('#brandSelect').val(product.brand_id);
    }, 500);
    
    $('#addProductModalLabel').html('<i class="fa fa-edit"></i> Edit Product');
    $('#addProductModal').modal('show');
}

// Delete product
function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product?')) {
        return;
    }
    
    $.ajax({
        url: '../actions/delete_product_action.php',
        method: 'POST',
        data: { product_id: productId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showAlert(response.message, 'success');
                loadProducts();
            } else {
                showAlert(response.message || 'Error deleting product', 'danger');
            }
        },
        error: function(xhr, status, error) {
            showAlert('Error: ' + error, 'danger');
        }
    });
}

// Upload bulk CSV
function uploadBulkCSV() {
    const fileInput = document.getElementById('csvFile');
    if (!fileInput.files || fileInput.files.length === 0) {
        showAlert('Please select a CSV file', 'warning');
        return;
    }
    
    const formData = new FormData($('#bulkUploadForm')[0]);
    formData.append('csv_file', fileInput.files[0]);
    
    $('#csvUploadStatus').html('<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> <span>Uploading and processing CSV...</span></div>');
    
    $.ajax({
        url: '../actions/bulk_upload_products_action.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showAlert(response.message, 'success');
                $('#bulkUploadModal').modal('hide');
                $('#bulkUploadForm')[0].reset();
                $('#csvUploadStatus').html('');
                loadProducts();
            } else {
                showAlert(response.message || 'Error uploading CSV', 'danger');
                $('#csvUploadStatus').html('<div class="alert alert-danger">' + (response.message || 'Error') + '</div>');
            }
        },
        error: function(xhr, status, error) {
            let errorMsg = 'Error uploading CSV: ' + error;
            if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {}
            }
            showAlert(errorMsg, 'danger');
            $('#csvUploadStatus').html('<div class="alert alert-danger">' + errorMsg + '</div>');
        }
    });
}

// Show alert
function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('#alertContainer').html(alertHtml).show();
    setTimeout(() => {
        $('#alertContainer').fadeOut();
    }, 5000);
}

