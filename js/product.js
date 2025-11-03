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
    
    $('#bulkUploadImageModal').on('shown.bs.modal', function() {
        $(this).removeAttr('aria-hidden');
    });
    
    $('#bulkUploadImageModal').on('hidden.bs.modal', function() {
        $(this).attr('aria-hidden', 'true');
    });
    
    // Show selected files count for bulk upload with size validation
    $(document).on('change', '#bulkProductImages', function() {
        const files = this.files;
        const count = files.length;
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        
        if (count > 0) {
            let fileList = '<div class="alert alert-info"><strong>Selected ' + count + ' file(s):</strong><ul class="mb-0 mt-2">';
            let hasErrors = false;
            let errorList = '';
            
            for (let i = 0; i < Math.min(count, 5); i++) {
                const file = files[i];
                const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
                const isValidType = allowedTypes.includes(file.type);
                const isValidSize = file.size <= maxSize;
                
                let statusIcon = '';
                let statusClass = '';
                
                if (!isValidType) {
                    statusIcon = ' ❌';
                    statusClass = 'text-danger';
                    hasErrors = true;
                    errorList += '<li class="text-danger"><strong>' + file.name + '</strong>: Invalid file type. Only JPEG, PNG, GIF allowed.</li>';
                } else if (!isValidSize) {
                    statusIcon = ' ⚠️';
                    statusClass = 'text-warning';
                    hasErrors = true;
                    errorList += '<li class="text-warning"><strong>' + file.name + '</strong>: File too large (' + fileSizeMB + ' MB). Maximum 5MB per file.</li>';
                } else {
                    statusIcon = ' ✓';
                    statusClass = 'text-success';
                }
                
                fileList += '<li class="' + statusClass + '">' + file.name + ' (' + fileSizeMB + ' MB)' + statusIcon + '</li>';
            }
            
            if (count > 5) {
                fileList += '<li><em>... and ' + (count - 5) + ' more file(s)</em></li>';
            }
            
            fileList += '</ul></div>';
            
            // Show errors if any
            if (hasErrors) {
                fileList += '<div class="alert alert-danger mt-2"><strong>⚠️ Upload Warnings:</strong><ul class="mb-0 mt-2">' + errorList + '</ul></div>';
            }
            
            $('#selectedFiles').html(fileList);
        } else {
            $('#selectedFiles').html('');
        }
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
    const container = $('#productsContainer');
    container.html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-3">Loading products...</p></div>');
    
    $.ajax({
        url: '../actions/fetch_product_action.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                products = response.data || [];
                displayProducts();
            } else {
                container.html('<div class="alert alert-danger">Error loading products: ' + (response.message || 'Unknown error') + '</div>');
                showAlert('Error loading products: ' + (response.message || 'Unknown error'), 'danger');
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
            container.html('<div class="alert alert-danger">' + errorMsg + '<br><button class="btn btn-sm btn-primary mt-2" onclick="loadProducts()">Retry</button></div>');
            showAlert(errorMsg, 'danger');
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
        let imagePath = product.product_image ? product.product_image : null;
        // Ensure image path is correct (add ../ if needed for admin view)
        if (imagePath && !imagePath.startsWith('http') && !imagePath.startsWith('../')) {
            imagePath = '../' + imagePath;
        }
        const imageHtml = imagePath ? 
            `<img src="${imagePath}" alt="${product.product_title}" onerror="this.parentElement.innerHTML='<div class=\\'product-image-placeholder\\'><i class=\\'fa fa-image\\'></i></div>';">` : 
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
                        <button class="btn btn-success btn-sm" onclick="bulkUploadImages(${product.product_id})">
                            <i class="fa fa-images"></i> Bulk Upload
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
    if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        return;
    }
    
    $.ajax({
        url: '../actions/delete_product_action.php',
        method: 'POST',
        data: {
            product_id: productId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showAlert('Product deleted successfully!', 'success');
                // Reload products to reflect deletion
                loadProducts();
            } else {
                showAlert('Error deleting product: ' + response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Delete error:', error);
            showAlert('Error deleting product. Please try again.', 'danger');
        }
    });
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

function bulkUploadImages(productId) {
    $('#bulkUploadProductId').val(productId);
    $('#bulkUploadImageModal').modal('show');
    
    // Reset form when modal opens
    $('#bulkUploadImageForm')[0].reset();
    $('#selectedFiles').html('');
    $('#bulkUploadStatus').html('');
}

function saveBulkImages() {
    const productId = $('#bulkUploadProductId').val();
    const fileInput = document.getElementById('bulkProductImages');
    
    if (!fileInput.files || fileInput.files.length === 0) {
        showAlert('Please select at least one image file', 'warning');
        return;
    }
    
    // Validate file count (max 10 images at once)
    if (fileInput.files.length > 10) {
        showAlert('Maximum 10 images can be uploaded at once', 'warning');
        return;
    }
    
    // Validate each file before upload
    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    const invalidFiles = [];
    
    for (let i = 0; i < fileInput.files.length; i++) {
        const file = fileInput.files[i];
        const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);
        
        // Check file type
        if (!allowedTypes.includes(file.type)) {
            invalidFiles.push(file.name + ': Invalid file type (only JPEG, PNG, GIF allowed)');
            continue;
        }
        
        // Check file size
        if (file.size > maxSize) {
            invalidFiles.push(file.name + ': File too large (' + fileSizeMB + ' MB, max 5MB)');
            continue;
        }
    }
    
    // If any invalid files, show error and prevent upload
    if (invalidFiles.length > 0) {
        let errorMsg = 'Cannot upload files with errors:<ul>';
        invalidFiles.forEach(function(error) {
            errorMsg += '<li>' + error + '</li>';
        });
        errorMsg += '</ul>Please remove invalid files and try again.';
        showAlert(errorMsg, 'danger');
        return;
    }
    
    const formData = new FormData();
    formData.append('product_id', productId);
    
    // Append all files - use array notation for PHP to process multiple files
    // PHP expects files with [] to create array structure in $_FILES
    for (let i = 0; i < fileInput.files.length; i++) {
        formData.append('product_images[]', fileInput.files[i]);
    }
    
    // Debug: log what we're sending
    console.log('Uploading ' + fileInput.files.length + ' file(s)');
    
    // Show loading
    $('#bulkUploadStatus').html('<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> <span>Uploading images...</span></div>');
    
    $.ajax({
        url: '../actions/bulk_upload_images_action.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let message = response.message;
                if (response.partial && response.errors) {
                    message += '<br>Errors:<ul>';
                    response.errors.forEach(function(error) {
                        message += '<li>' + error + '</li>';
                    });
                    message += '</ul>';
                }
                showAlert(message, response.partial ? 'warning' : 'success');
                
                // Reset form
                $('#bulkUploadImageForm')[0].reset();
                $('#selectedFiles').html('');
                $('#bulkUploadStatus').html('');
                
                // Close modal
                $('#bulkUploadImageModal').modal('hide');
                
                // Reload products to show new images
                setTimeout(function() {
                    loadProducts();
                }, 500);
            } else {
                showAlert('Error uploading images: ' + response.message, 'danger');
                $('#bulkUploadStatus').html('');
            }
        },
        error: function(xhr, status, error) {
            console.error('Bulk upload error:', error);
            showAlert('Error uploading images. Please try again.', 'danger');
            $('#bulkUploadStatus').html('');
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
