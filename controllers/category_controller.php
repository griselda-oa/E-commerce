<?php
require_once __DIR__ . '/../classes/category_class.php';

class CategoryController {
    private $category;
    
    public function __construct() {
        $this->category = new Category();
    }
    
    /**
     * Add a new category
     * @param array $kwargs - Category data
     * @return array - Response
     */
    public function add_category_ctr($kwargs) {
        return $this->category->add($kwargs);
    }
    
    /**
     * Get all categories
     * @return array - Response
     */
    public function get_categories_ctr() {
        return $this->category->getAllCategories();
    }
    
    /**
     * Get a specific category
     * @param int $category_id - Category ID
     * @return array - Response
     */
    public function get_category_ctr($category_id) {
        return $this->category->getCategoryById($category_id);
    }
    
    /**
     * Update a category
     * @param array $kwargs - Category data
     * @return array - Response
     */
    public function update_category_ctr($kwargs) {
        return $this->category->update($kwargs);
    }
    
    /**
     * Delete a category
     * @param int $category_id - Category ID
     * @return array - Response
     */
    public function delete_category_ctr($category_id) {
        return $this->category->delete($category_id);
    }
    
    /**
     * Get category count
     * @return int - Count
     */
    public function get_category_count_ctr() {
        return $this->category->getCategoryCount();
    }
}
?>