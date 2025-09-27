class CategoryManager {
    constructor() {
        this.categories = [];
        this.currentEditId = null;
        this.currentDeleteId = null;
        this.currentDeleteName = null;
        
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Add category form
        document.getElementById('addCategoryForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleAddCategory();
        });

        // Edit category form
        document.getElementById('editCategoryForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleEditCategory();
        });

        // Delete confirmation button
        document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
            this.handleDeleteCategory();
        });

        // Clear form data when modals are closed
        document.getElementById('addCategoryModal').addEventListener('hidden.bs.modal', () => {
            this.clearAddForm();
        });

        document.getElementById('editCategoryModal').addEventListener('hidden.bs.modal', () => {
            this.clearEditForm();
        });

        document.getElementById('deleteCategoryModal').addEventListener('hidden.bs.modal', () => {
            this.clearDeleteData();
        });
    }

    async loadCategories() {
        try {
            this.showLoading();
            
            const response = await fetch('../actions/fetch_category_action.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const result = await response.json();
            
            if (result.status === 'success') {
                this.categories = result.categories || [];
                this.displayCategories();
                this.updateCategoryCount();
            } else {
                this.showAlert('error', result.message || 'Failed to load categories');
            }
        } catch (error) {
            console.error('Error loading categories:', error);
            this.showAlert('error', 'An error occurred while loading categories');
        } finally {
            this.hideLoading();
        }
    }

    /**
     * Display categories in the table
     */
    displayCategories() {
        const tableBody = document.getElementById('categoriesTableBody');
        const noCategoriesMessage = document.getElementById('noCategoriesMessage');
        const table = document.getElementById('categoriesTable');

        if (this.categories.length === 0) {
            table.style.display = 'none';
            noCategoriesMessage.style.display = 'block';
            return;
        }

        table.style.display = 'table';
        noCategoriesMessage.style.display = 'none';

        tableBody.innerHTML = this.categories.map(category => `
            <tr>
                <td>${category.cat_id}</td>
                <td>${this.escapeHtml(category.cat_name)}</td>
                <td>
                    <button class="btn btn-sm btn-edit" onclick="categoryManager.editCategory(${category.cat_id}, '${this.escapeHtml(category.cat_name)}')" title="Edit Category">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-delete" onclick="categoryManager.deleteCategory(${category.cat_id}, '${this.escapeHtml(category.cat_name)}')" title="Delete Category">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    /**
     * Update category count display
     */
    updateCategoryCount() {
        const countElement = document.getElementById('categoryCount');
        countElement.textContent = this.categories.length;
    }

    /**
     * Handle add category form submission
     */
    async handleAddCategory() {
        const form = document.getElementById('addCategoryForm');
        const formData = new FormData(form);
        const catName = formData.get('cat_name').trim();

        // Validate input
        if (!this.validateCategoryName(catName)) {
            return;
        }

        try {
            this.showLoading();

            const response = await fetch('../actions/add_category_action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
                this.showAlert('success', result.message || 'Category added successfully');
                this.clearAddForm();
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
                modal.hide();
                
                // Reload categories
                await this.loadCategories();
            } else {
                this.showAlert('error', result.message || 'Failed to add category');
            }
        } catch (error) {
            console.error('Error adding category:', error);
            this.showAlert('error', 'An error occurred while adding the category');
        } finally {
            this.hideLoading();
        }
    }

    /**
     * Handle edit category form submission
     */
    async handleEditCategory() {
        const form = document.getElementById('editCategoryForm');
        const formData = new FormData(form);
        const catName = formData.get('cat_name').trim();

        // Validate input
        if (!this.validateCategoryName(catName)) {
            return;
        }

        try {
            this.showLoading();

            const response = await fetch('../actions/update_category_action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
                this.showAlert('success', result.message || 'Category updated successfully');
                this.clearEditForm();
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
                modal.hide();
                
                // Reload categories
                await this.loadCategories();
            } else {
                this.showAlert('error', result.message || 'Failed to update category');
            }
        } catch (error) {
            console.error('Error updating category:', error);
            this.showAlert('error', 'An error occurred while updating the category');
        } finally {
            this.hideLoading();
        }
    }

    /**
     * Handle delete category
     */
    async handleDeleteCategory() {
        if (!this.currentDeleteId) {
            this.showAlert('error', 'No category selected for deletion');
            return;
        }

        try {
            this.showLoading();

            const formData = new FormData();
            formData.append('cat_id', this.currentDeleteId);

            const response = await fetch('../actions/delete_category_action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.status === 'success') {
                this.showAlert('success', result.message || 'Category deleted successfully');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal'));
                modal.hide();
                
                // Reload categories
                await this.loadCategories();
            } else {
                this.showAlert('error', result.message || 'Failed to delete category');
            }
        } catch (error) {
            console.error('Error deleting category:', error);
            this.showAlert('error', 'An error occurred while deleting the category');
        } finally {
            this.hideLoading();
        }
    }

    /**
     * Open edit category modal
     */
    editCategory(catId, catName) {
        this.currentEditId = catId;
        document.getElementById('editCategoryId').value = catId;
        document.getElementById('editCategoryName').value = catName;
        
        const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        modal.show();
    }

    /**
     * Open delete category modal
     */
    deleteCategory(catId, catName) {
        this.currentDeleteId = catId;
        this.currentDeleteName = catName;
        document.getElementById('deleteCategoryName').textContent = catName;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
        modal.show();
    }

    /**
     * Validate category name
     */
    validateCategoryName(name) {
        if (!name || name.trim().length === 0) {
            this.showAlert('error', 'Category name cannot be empty');
            return false;
        }

        if (name.length > 100) {
            this.showAlert('error', 'Category name must be less than 100 characters');
            return false;
        }

        // Check for duplicate names (excluding current edit)
        const existingNames = this.categories
            .filter(cat => cat.cat_id !== this.currentEditId)
            .map(cat => cat.cat_name.toLowerCase());
        
        if (existingNames.includes(name.toLowerCase())) {
            this.showAlert('error', 'Category name already exists');
            return false;
        }

        return true;
    }

    /**
     * Show alert message
     */
    showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${this.escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        alertContainer.innerHTML = alertHtml;
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }
    }

    /**
     * Show loading state
     */
    showLoading() {
        // Disable all buttons and inputs
        const buttons = document.querySelectorAll('button, input[type="submit"]');
        buttons.forEach(btn => {
            btn.disabled = true;
            if (btn.classList.contains('btn')) {
                btn.classList.add('disabled');
            }
        });
    }

    /**
     * Hide loading state
     */
    hideLoading() {
        // Enable all buttons and inputs
        const buttons = document.querySelectorAll('button, input[type="submit"]');
        buttons.forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('disabled');
        });
    }

    /**
     * Clear add form
     */
    clearAddForm() {
        document.getElementById('addCategoryForm').reset();
    }

    /**
     * Clear edit form
     */
    clearEditForm() {
        document.getElementById('editCategoryForm').reset();
        this.currentEditId = null;
    }

    /**
     * Clear delete data
     */
    clearDeleteData() {
        this.currentDeleteId = null;
        this.currentDeleteName = null;
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize category manager when DOM is loaded
let categoryManager;
document.addEventListener('DOMContentLoaded', function() {
    categoryManager = new CategoryManager();
});

// Global functions for inline event handlers
window.loadCategories = function() {
    if (categoryManager) {
        categoryManager.loadCategories();
    }
};

window.editCategory = function(catId, catName) {
    if (categoryManager) {
        categoryManager.editCategory(catId, catName);
    }
};

window.deleteCategory = function(catId, catName) {
    if (categoryManager) {
        categoryManager.deleteCategory(catId, catName);
    }
};
