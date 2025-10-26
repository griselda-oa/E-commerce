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
     * Get all products for a user
     * @param int $user_id - User ID
     * @return array - Response
     */
    public function get_products_ctr($user_id) {
        return $this->product->getProductsByUser($user_id);
    }
    
    /**
     * Get a specific product
     * @param int $product_id - Product ID
     * @param int $user_id - User ID
     * @return array - Response
     */
    public function get_product_ctr($product_id, $user_id) {
        return $this->product->getProductById($product_id, $user_id);
    }
}
?>
