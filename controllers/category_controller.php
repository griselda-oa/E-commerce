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
     * Get all categories for a user
     * @param int $user_id - User ID
     * @return array - Response
     */
    public function get_categories_ctr($user_id) {
        return $this->category->getCategoriesByUser($user_id);
    }
    
    /**
     * Get a specific category
     * @param int $category_id - Category ID
     * @param int $user_id - User ID
     * @return array - Response
     */
    public function get_category_ctr($category_id, $user_id) {
        return $this->category->getCategoryById($category_id, $user_id);
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
     * @param int $user_id - User ID
     * @return array - Response
     */
    public function delete_category_ctr($category_id, $user_id) {
        return $this->category->delete($category_id, $user_id);
    }
    
    /**
     * Get category count for a user
     * @param int $user_id - User ID
     * @return int - Count
     */
    public function get_category_count_ctr($user_id) {
        return $this->category->getCategoryCount($user_id);
    }
}
?>