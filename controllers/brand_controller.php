<?php
// controllers/brand_controller.php
require_once __DIR__ . '/../classes/brand_class.php';

class BrandController {
    private $brand;
    
    public function __construct() {
        $this->brand = new Brand();
    }
    
    /**
     * Add a new brand
     * @param array $kwargs - Brand data
     * @return array - Response
     */
    public function add_brand_ctr($kwargs) {
        return $this->brand->add($kwargs);
    }
    
    /**
     * Get all brands for a user
     * @param int $user_id - User ID
     * @return array - Response
     */
    public function get_brands_ctr($user_id) {
        return $this->brand->getBrandsByUser($user_id);
    }
    
    /**
     * Get a specific brand
     * @param int $brand_id - Brand ID
     * @param int $user_id - User ID
     * @return array - Response
     */
    public function get_brand_ctr($brand_id, $user_id) {
        return $this->brand->getBrandById($brand_id, $user_id);
    }
    
    /**
     * Update a brand
     * @param array $kwargs - Brand data
     * @return array - Response
     */
    public function update_brand_ctr($kwargs) {
        return $this->brand->update($kwargs);
    }
    
    /**
     * Delete a brand
     * @param int $brand_id - Brand ID
     * @param int $user_id - User ID
     * @return array - Response
     */
    public function delete_brand_ctr($brand_id, $user_id) {
        return $this->brand->delete($brand_id, $user_id);
    }
}
?>
