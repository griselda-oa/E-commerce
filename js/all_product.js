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
                <div class="empty-state">
                    <i class="fa fa-search"></i>
                    <h4>No Products Found</h4>
                    <p>Try adjusting your search criteria or browse all products</p>
                    <button class="btn btn-primary btn-lg mt-3" onclick="loadAllProducts()">
                        <i class="fa fa-refresh"></i> Show All Products
                    </button>
                </div>
            `);
            return;
        }

        let html = '<div class="product-grid">';
        pageProducts.forEach(product => {
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
                        <p class="product-description">${product.product_desc ? product.product_desc.substring(0, 120) + '...' : 'No description available'}</p>
                        <a href="single_product.php?token=${product.product_token}" class="btn btn-add-cart">
                            <i class="fa fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
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
