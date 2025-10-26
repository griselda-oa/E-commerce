// js/category.js
$(document).ready(function() {
    // Load categories on page load
    loadCategories();
});

// Global variables
let categories = [];

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

// Load categories from server
async function loadCategories() {
    try {
        const response = await fetch('../actions/fetch_category_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        });

        const result = await response.json();
        
        if (result.success) {
            categories = result.data;
            displayCategories();
        } else {
            showAlert('Error loading categories: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        showAlert('Error loading categories. Please try again.', 'danger');
    }
}

// Display categories in table
function displayCategories() {
    const tbody = document.getElementById('categoriesTableBody');
    
    if (categories.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No categories found. Add your first category!</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = categories.map(category => `
        <tr>
            <td><strong>#${category.cat_id}</strong></td>
            <td>${escapeHtml(category.cat_name)}</td>
            <td>${new Date().toLocaleDateString()}</td>
            <td>
                <button class="btn btn-edit me-2" onclick="editCategory(${category.cat_id})">
                    <i class="fa fa-edit"></i> Edit
                </button>
                <button class="btn btn-delete" onclick="confirmDelete(${category.cat_id}, '${escapeHtml(category.cat_name)}')">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </td>
        </tr>
    `).join('');
}

// Add new category
async function addCategory() {
    const categoryName = document.getElementById('categoryName').value.trim();
    
    if (!categoryName) {
        showAlert('Please enter a category name', 'warning');
        return;
    }

    // Validate category name
    if (categoryName.length > 100) {
        showAlert('Category name must be 100 characters or less', 'warning');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('category_name', categoryName);

        const response = await fetch('../actions/add_category_action.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            showAlert('Category added successfully!', 'success');
            document.getElementById('addCategoryForm').reset();
            bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();
            loadCategories();
        } else {
            showAlert('Error adding category: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error adding category:', error);
        showAlert('Error adding category. Please try again.', 'danger');
    }
}

// Edit category
function editCategory(categoryId) {
    const category = categories.find(cat => cat.cat_id == categoryId);
    if (!category) {
        showAlert('Category not found', 'danger');
        return;
    }

    document.getElementById('editCategoryId').value = category.cat_id;
    document.getElementById('editCategoryName').value = category.cat_name;
    
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}

// Update category
async function updateCategory() {
    const categoryId = document.getElementById('editCategoryId').value;
    const categoryName = document.getElementById('editCategoryName').value.trim();
    
    if (!categoryName) {
        showAlert('Please enter a category name', 'warning');
        return;
    }

    // Validate category name
    if (categoryName.length > 100) {
        showAlert('Category name must be 100 characters or less', 'warning');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('category_id', categoryId);
        formData.append('category_name', categoryName);

        const response = await fetch('../actions/update_category_action.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            showAlert('Category updated successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editCategoryModal')).hide();
            loadCategories();
        } else {
            showAlert('Error updating category: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error updating category:', error);
        showAlert('Error updating category. Please try again.', 'danger');
    }
}

// Confirm delete
function confirmDelete(categoryId, categoryName) {
    document.getElementById('deleteCategoryId').value = categoryId;
    new bootstrap.Modal(document.getElementById('deleteCategoryModal')).show();
}

// Delete category
async function deleteCategory() {
    const categoryId = document.getElementById('deleteCategoryId').value;

    try {
        const formData = new FormData();
        formData.append('category_id', categoryId);

        const response = await fetch('../actions/delete_category_action.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            showAlert('Category deleted successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal')).hide();
            loadCategories();
        } else {
            showAlert('Error deleting category: ' + result.message, 'danger');
        }
    } catch (error) {
        console.error('Error deleting category:', error);
        showAlert('Error deleting category. Please try again.', 'danger');
    }
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
