$(document).ready(function() {
    let allProducts = [];
    let filteredProducts = [];
    let currentPage = 1;
    const productsPerPage = 10;

    // Load all data
    loadProducts();
    loadCategories();
    loadBrands();

    // Search functionality
    $('#searchBtn').click(function() {
        filterProducts();
    });

    $('#searchInput').keypress(function(e) {
        if (e.which === 13) {
            filterProducts();
        }
    });

    // Category filter change
    $('#categoryFilter').change(function() {
        loadBrandsForCategory($(this).val());
        filterProducts();
    });

    // Brand filter change
    $('#brandFilter').change(function() {
        filterProducts();
    });

    function loadProducts() {
        $.ajax({
            url: 'actions/fetch_all_products_action.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    allProducts = response.data;
                    filteredProducts = [...allProducts];
                    displayProducts();
                    updatePagination();
                } else {
                    showAlert('Error loading products: ' + response.message, 'danger');
                }
            },
            error: function() {
                showAlert('Error connecting to server', 'danger');
            }
        });
    }

    function loadCategories() {
        $.ajax({
            url: 'actions/fetch_category_action.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const select = $('#categoryFilter');
                    response.data.forEach(category => {
                        select.append(`<option value="${category.cat_id}">${category.cat_name}</option>`);
                    });
                }
            }
        });
    }

    function loadBrands() {
        $.ajax({
            url: 'actions/fetch_all_brands_action.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const select = $('#brandFilter');
                    response.data.forEach(brand => {
                        select.append(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
                    });
                }
            }
        });
    }

    function loadBrandsForCategory(catId) {
        if (!catId) {
            $('#brandFilter').html('<option value="">All Brands</option>');
            return;
        }

        $.ajax({
            url: 'actions/fetch_brand_action.php',
            method: 'POST',
            data: { cat_id: catId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const select = $('#brandFilter');
                    select.html('<option value="">All Brands</option>');
                    response.data.forEach(brand => {
                        select.append(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
                    });
                }
            }
        });
    }

    function filterProducts() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const categoryId = $('#categoryFilter').val();
        const brandId = $('#brandFilter').val();

        filteredProducts = allProducts.filter(product => {
            const matchesSearch = !searchTerm || 
                product.product_title.toLowerCase().includes(searchTerm) ||
                product.product_description.toLowerCase().includes(searchTerm) ||
                product.product_keyword.toLowerCase().includes(searchTerm);
            
            const matchesCategory = !categoryId || product.cat_id == categoryId;
            const matchesBrand = !brandId || product.brand_id == brandId;

            return matchesSearch && matchesCategory && matchesBrand;
        });

        currentPage = 1;
        displayProducts();
        updatePagination();
    }

    function displayProducts() {
        const container = $('#productsContainer');
        const startIndex = (currentPage - 1) * productsPerPage;
        const endIndex = startIndex + productsPerPage;
        const pageProducts = filteredProducts.slice(startIndex, endIndex);

        if (pageProducts.length === 0) {
            container.html(`
                <div class="col-12 text-center py-5">
                    <i class="fa fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No products found</h4>
                    <p class="text-muted">Try adjusting your search criteria</p>
                </div>
            `);
            return;
        }

        let html = '';
        pageProducts.forEach(product => {
            const imagePath = product.product_image ? product.product_image : 'https://via.placeholder.com/300x200?text=No+Image';
            html += `
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="product-card h-100">
                        <img src="${imagePath}" alt="${product.product_title}" class="product-image mb-3">
                        <h5 class="card-title">${product.product_title}</h5>
                        <p class="text-muted small">${product.cat_name} - ${product.brand_name}</p>
                        <p class="h4 text-primary mb-3">GHS ${parseFloat(product.product_price).toFixed(2)}</p>
                        <p class="card-text text-truncate">${product.product_description}</p>
                        <div class="mt-auto">
                            <a href="single_product.php?id=${product.product_id}" class="btn btn-primary w-100">
                                <i class="fa fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });

        container.html(html);
    }

    function updatePagination() {
        const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
        const pagination = $('#pagination');

        if (totalPages <= 1) {
            pagination.html('');
            return;
        }

        let html = '<nav><ul class="pagination justify-content-center">';
        
        // Previous button
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a>
        </li>`;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
            </li>`;
        }

        // Next button
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a>
        </li>`;

        html += '</ul></nav>';
        pagination.html(html);
    }

    window.changePage = function(page) {
        const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            displayProducts();
            updatePagination();
        }
    };

    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('body').prepend(alertHtml);
        setTimeout(() => $('.alert').fadeOut(), 5000);
    }
});
