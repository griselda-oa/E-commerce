<?php
// controllers/category_controller.php
require_once __DIR__ . '/../classes/category_class.php';

class CategoryController
{
    private $category;

    public function __construct()
    {
        $this->category = new Category();
    }

    /**
     * Add a new category
     * @param array $kwargs Array containing 'cat_name' and 'user_id'
     * @return array Result array with status and message
     */
    public function add_category_ctr($kwargs)
    {
        // Validate required parameters
        if (!isset($kwargs['cat_name']) || !isset($kwargs['user_id'])) {
            return [
                'status' => 'error',
                'message' => 'Category name and user ID are required.'
            ];
        }

        $cat_name = trim($kwargs['cat_name']);
        $user_id = (int)$kwargs['user_id'];

        // Validate category name
        if (empty($cat_name)) {
            return [
                'status' => 'error',
                'message' => 'Category name cannot be empty.'
            ];
        }

        if (strlen($cat_name) > 100) {
            return [
                'status' => 'error',
                'message' => 'Category name must be less than 100 characters.'
            ];
        }

        // Attempt to add category
        $result = $this->category->add_category($cat_name, $user_id);

        if ($result !== false) {
            return [
                'status' => 'success',
                'message' => 'Category added successfully.',
                'category_id' => $result
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to add category. Category name may already exist.'
            ];
        }
    }

    /**
     * Fetch all categories for a user
     * @param array $kwargs Array containing 'user_id'
     * @return array Result array with status and data
     */
    public function fetch_categories_ctr($kwargs)
    {
        // Validate required parameters
        if (!isset($kwargs['user_id'])) {
            return [
                'status' => 'error',
                'message' => 'User ID is required.'
            ];
        }

        $user_id = (int)$kwargs['user_id'];

        // Fetch categories
        $categories = $this->category->get_categories_by_user($user_id);

        if ($categories !== false) {
            return [
                'status' => 'success',
                'message' => 'Categories fetched successfully.',
                'categories' => $categories,
                'count' => count($categories)
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch categories.'
            ];
        }
    }

    /**
     * Update a category
     * @param array $kwargs Array containing 'cat_id', 'cat_name', and 'user_id'
     * @return array Result array with status and message
     */
    public function update_category_ctr($kwargs)
    {
        // Validate required parameters
        if (!isset($kwargs['cat_id']) || !isset($kwargs['cat_name']) || !isset($kwargs['user_id'])) {
            return [
                'status' => 'error',
                'message' => 'Category ID, name, and user ID are required.'
            ];
        }

        $cat_id = (int)$kwargs['cat_id'];
        $cat_name = trim($kwargs['cat_name']);
        $user_id = (int)$kwargs['user_id'];

        // Validate category name
        if (empty($cat_name)) {
            return [
                'status' => 'error',
                'message' => 'Category name cannot be empty.'
            ];
        }

        if (strlen($cat_name) > 100) {
            return [
                'status' => 'error',
                'message' => 'Category name must be less than 100 characters.'
            ];
        }

        // Attempt to update category
        $result = $this->category->update_category($cat_id, $cat_name, $user_id);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Category updated successfully.'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to update category. Category may not exist or name may already be in use.'
            ];
        }
    }

    /**
     * Delete a category
     * @param array $kwargs Array containing 'cat_id' and 'user_id'
     * @return array Result array with status and message
     */
    public function delete_category_ctr($kwargs)
    {
        // Validate required parameters
        if (!isset($kwargs['cat_id']) || !isset($kwargs['user_id'])) {
            return [
                'status' => 'error',
                'message' => 'Category ID and user ID are required.'
            ];
        }

        $cat_id = (int)$kwargs['cat_id'];
        $user_id = (int)$kwargs['user_id'];

        // Attempt to delete category
        $result = $this->category->delete_category($cat_id, $user_id);

        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Category deleted successfully.'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to delete category. Category may not exist or you may not have permission.'
            ];
        }
    }

    /**
     * Get a specific category
     * @param array $kwargs Array containing 'cat_id' and 'user_id'
     * @return array Result array with status and data
     */
    public function get_category_ctr($kwargs)
    {
        // Validate required parameters
        if (!isset($kwargs['cat_id']) || !isset($kwargs['user_id'])) {
            return [
                'status' => 'error',
                'message' => 'Category ID and user ID are required.'
            ];
        }

        $cat_id = (int)$kwargs['cat_id'];
        $user_id = (int)$kwargs['user_id'];

        // Get category
        $category = $this->category->get_category_by_id($cat_id, $user_id);

        if ($category !== false) {
            return [
                'status' => 'success',
                'message' => 'Category retrieved successfully.',
                'category' => $category
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Category not found or you may not have permission to view it.'
            ];
        }
    }

    /**
     * Get category count for a user
     * @param array $kwargs Array containing 'user_id'
     * @return array Result array with status and count
     */
    public function get_category_count_ctr($kwargs)
    {
        // Validate required parameters
        if (!isset($kwargs['user_id'])) {
            return [
                'status' => 'error',
                'message' => 'User ID is required.'
            ];
        }

        $user_id = (int)$kwargs['user_id'];

        // Get count
        $count = $this->category->get_category_count($user_id);

        return [
            'status' => 'success',
            'message' => 'Category count retrieved successfully.',
            'count' => $count
        ];
    }
}

