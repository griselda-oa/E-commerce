<?php
// controllers/product_controller.php
require_once __DIR__ . '/../classes/product_class.php';

class ProductController {
    private $product;
    
    public function __construct() {
        $this->product = new Product();
    }
    
    /**
     * Add a new product
     * @param array $kwargs - Product data
     * @return array - Response
     */
    public function add_product_ctr($kwargs) {
        return $this->product->add($kwargs);
    }
    
    /**
     * Update a product
     * @param array $kwargs - Product data
     * @return array - Response
     */
    public function update_product_ctr($kwargs) {
        return $this->product->update($kwargs);
    }
    
    /**
     * Get all products
     * @return array - Response
     */
    public function get_products_ctr() {
        return $this->product->getAllProducts();
    }
    
    /**
     * Get a product by secure token (public access)
     * @param string $token - Secure product token
     * @return array - Response
     */
    public function get_product_by_token_ctr($token) {
        return $this->product->getProductByToken($token);
    }
    
    /**
     * Search products
     * @param string $query - Search query
     * @return array - Response
     */
    public function search_products_ctr($query) {
        return $this->product->searchProducts($query);
    }
    
    /**
     * Filter products by category
     * @param int $cat_id - Category ID
     * @return array - Response
     */
    public function filter_by_category_ctr($cat_id) {
        return $this->product->filterProductsByCategory($cat_id);
    }
    
    /**
     * Filter products by brand
     * @param int $brand_id - Brand ID
     * @return array - Response
     */
    public function filter_by_brand_ctr($brand_id) {
        return $this->product->filterProductsByBrand($brand_id);
    }
}
?>