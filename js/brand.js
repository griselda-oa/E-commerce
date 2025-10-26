// js/brand.js
$(document).ready(function() {
    // Load brands on page load
    loadBrands();
});

// Global variables
let brands = [];

// Show alert message
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer');
    const alertId = 'alert-' + Date.now();
    
    alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" id="${alertId}" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Load brands from server
async function loadBrands() {
    try {
        const response = await fetch('../actions/fetch_brand_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        });

        const result = await response.json();
        
        if (result.success) {
            brands = result.data;
            displayBrands();
        } else {
            showAlert('Error loading brands: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error loading brands:', error);
        showAlert('Error loading brands. Please try again.', 'danger');
    }
}

// Display brands in table, organized by category
function displayBrands() {
    const tbody = document.getElementById('brandsTableBody');
    
    if (brands.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No brands found. Add your first brand!</p>
                </td>
            </tr>
        `;
        return;
    }

    // Group brands by category
    const groupedBrands = {};
    brands.forEach(brand => {
        if (!groupedBrands[brand.cat_name]) {
            groupedBrands[brand.cat_name] = [];
        }
        groupedBrands[brand.cat_name].push(brand);
    });

    let html = '';
    let categoryCount = Object.keys(groupedBrands).length;
    let currentIndex = 0;

    Object.keys(groupedBrands).sort().forEach(categoryName => {
        const categoryBrands = groupedBrands[categoryName];
        
        categoryBrands.forEach((brand, index) => {
            if (index === 0) {
                html += `
                    <tr style="background: #f0f4f8;">
                        <td colspan="4" style="font-weight: 700; color: #4f46e5;">
                            <i class="fa fa-folder"></i> ${escapeHtml(categoryName)}
                        </td>
                    </tr>
                `;
            }
            
            html += `
                <tr>
                    <td><strong>#${brand.brand_id}</strong></td>
                    <td><span class="badge bg-info">${escapeHtml(brand.cat_name)}</span></td>
                    <td>${escapeHtml(brand.brand_name)}</td>
                    <td>
                        <button class="btn btn-edit me-2" onclick="editBrand(${brand.brand_id})">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-delete" onclick="confirmDelete(${brand.brand_id}, '${escapeHtml(brand.brand_name)}')">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            `;
        });
    });

    tbody.innerHTML = html;
}

// Add new brand
async function addBrand() {
    const brandName = document.getElementById('brandName').value.trim();
    const catId = document.getElementById('addBrandCategory').value;
    
    if (!brandName) {
        showAlert('Please enter a brand name', 'warning');
        return;
    }

    if (!catId) {
        showAlert('Please select a category', 'warning');
        return;
    }

    // Validate brand name
    if (brandName.length > 100) {
        showAlert('Brand name must be 100 characters or less', 'warning');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('brand_name', brandName);
        formData.append('cat_id', catId);

        const response = await fetch('../actions/add_brand_action.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            showAlert('Brand added successfully!', 'success');
            document.getElementById('addBrandForm').reset();
            bootstrap.Modal.getInstance(document.getElementById('addBrandModal')).hide();
            loadBrands();
        } else {
            showAlert('Error adding brand: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error adding brand:', error);
        showAlert('Error adding brand. Please try again.', 'danger');
    }
}

// Edit brand
function editBrand(brandId) {
    const brand = brands.find(b => b.brand_id == brandId);
    if (!brand) {
        showAlert('Brand not found', 'danger');
        return;
    }

    document.getElementById('editBrandId').value = brand.brand_id;
    document.getElementById('editBrandName').value = brand.brand_name;
    document.getElementById('editBrandCategory').value = brand.cat_id;
    
    new bootstrap.Modal(document.getElementById('editBrandModal')).show();
}

// Update brand
async function updateBrand() {
    const brandId = document.getElementById('editBrandId').value;
    const brandName = document.getElementById('editBrandName').value.trim();
    const catId = document.getElementById('editBrandCategory').value;
    
    if (!brandName) {
        showAlert('Please enter a brand name', 'warning');
        return;
    }

    if (!catId) {
        showAlert('Please select a category', 'warning');
        return;
    }

    // Validate brand name
    if (brandName.length > 100) {
        showAlert('Brand name must be 100 characters or less', 'warning');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('brand_id', brandId);
        formData.append('brand_name', brandName);
        formData.append('cat_id', catId);

        const response = await fetch('../actions/update_brand_action.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            showAlert('Brand updated successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editBrandModal')).hide();
            loadBrands();
        } else {
            showAlert('Error updating brand: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error updating brand:', error);
        showAlert('Error updating brand. Please try again.', 'danger');
    }
}

// Confirm delete
function confirmDelete(brandId, brandName) {
    document.getElementById('deleteBrandId').value = brandId;
    new bootstrap.Modal(document.getElementById('deleteBrandModal')).show();
}

// Delete brand
async function deleteBrand() {
    const brandId = document.getElementById('deleteBrandId').value;

    try {
        const formData = new FormData();
        formData.append('brand_id', brandId);

        const response = await fetch('../actions/delete_brand_action.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            showAlert('Brand deleted successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('deleteBrandModal')).hide();
            loadBrands();
        } else {
            showAlert('Error deleting brand: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error deleting brand:', error);
        showAlert('Error deleting brand. Please try again.', 'danger');
    }
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
