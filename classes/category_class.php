<?php
require_once __DIR__ . '/../settings/db_class.php';

class Category extends db_connection {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Add a new category
     * @param array $args - Contains category data
     * @return array - Success/failure response
     */
    public function add($args) {
        try {
            $category_name = trim($args['category_name']);
            
            // Validate input
            if (empty($category_name)) {
                return array('success' => false, 'message' => 'Category name is required');
            }
            
            // Check if category name already exists
            if ($this->categoryExists($category_name)) {
                return array('success' => false, 'message' => 'Category name already exists');
            }
            
            // Insert new category
            $sql = "INSERT INTO categories (cat_name, user_id) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('si', $category_name, $args['user_id']);
            
            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Category added successfully', 'id' => $this->db->insert_id);
            } else {
                return array('success' => false, 'message' => 'Failed to add category: ' . $stmt->error);
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all categories created by a specific user
     * @param int $user_id - User ID
     * @return array - Categories data
     */
    public function getCategoriesByUser($user_id) {
        try {
            $sql = "SELECT * FROM categories WHERE user_id = ? ORDER BY cat_id DESC";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $categories = array();
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            
            return array('success' => true, 'data' => $categories);
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Get a specific category by ID
     * @param int $category_id - Category ID
     * @param int $user_id - User ID (for security)
     * @return array - Category data
     */
    public function getCategoryById($category_id, $user_id) {
        try {
            $sql = "SELECT * FROM categories WHERE cat_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('ii', $category_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                return array('success' => true, 'data' => $row);
            } else {
                return array('success' => false, 'message' => 'Category not found');
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Update a category
     * @param array $args - Contains category data
     * @return array - Success/failure response
     */
    public function update($args) {
        try {
            $category_id = $args['category_id'];
            $category_name = trim($args['category_name']);
            $user_id = $args['user_id'];
            
            // Validate input
            if (empty($category_name)) {
                return array('success' => false, 'message' => 'Category name is required');
            }
            
            // Check if category exists and belongs to user
            $category = $this->getCategoryById($category_id, $user_id);
            if (!$category['success']) {
                return $category;
            }
            
            // Check if new name already exists (excluding current category)
            if ($this->categoryExists($category_name, $category_id)) {
                return array('success' => false, 'message' => 'Category name already exists');
            }
            
            // Update category
            $sql = "UPDATE categories SET cat_name = ? WHERE cat_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('sii', $category_name, $category_id, $user_id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    return array('success' => true, 'message' => 'Category updated successfully');
                } else {
                    return array('success' => false, 'message' => 'No changes made');
                }
            } else {
                return array('success' => false, 'message' => 'Failed to update category: ' . $stmt->error);
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a category
     * @param int $category_id - Category ID
     * @param int $user_id - User ID (for security)
     * @return array - Success/failure response
     */
    public function delete($category_id, $user_id) {
        try {
            // Check if category exists and belongs to user
            $category = $this->getCategoryById($category_id, $user_id);
            if (!$category['success']) {
                return $category;
            }
            
            // Delete category
            $sql = "DELETE FROM categories WHERE cat_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('ii', $category_id, $user_id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    return array('success' => true, 'message' => 'Category deleted successfully');
                } else {
                    return array('success' => false, 'message' => 'Category not found');
                }
            } else {
                return array('success' => false, 'message' => 'Failed to delete category: ' . $stmt->error);
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if category name already exists
     * @param string $category_name - Category name
     * @param int $exclude_id - Category ID to exclude (for updates)
     * @return bool - True if exists, false otherwise
     */
    private function categoryExists($category_name, $exclude_id = null) {
        try {
            if ($exclude_id) {
                $sql = "SELECT COUNT(*) as count FROM categories WHERE cat_name = ? AND cat_id != ?";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('si', $category_name, $exclude_id);
            } else {
                $sql = "SELECT COUNT(*) as count FROM categories WHERE cat_name = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('s', $category_name);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return $row['count'] > 0;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get total count of categories for a user
     * @param int $user_id - User ID
     * @return int - Count of categories
     */
    public function getCategoryCount($user_id) {
        try {
            $sql = "SELECT COUNT(*) as count FROM categories WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return $row['count'];
            
        } catch (Exception $e) {
            return 0;
        }
    }
}
?>