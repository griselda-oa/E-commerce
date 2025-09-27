<?php
// classes/category_class.php
require_once __DIR__ . '/../settings/db_class.php';

class Category extends db_connection
{
    public function __construct() {
        parent::__construct(); // opens $this->db and sets utf8mb4
    }

    /**
     * Add a new category for a specific user
     * @param string $cat_name Category name
     * @param int $user_id User ID who created the category
     * @return int|false Returns category ID on success, false on failure
     */
    public function add_category(string $cat_name, int $user_id)
    {
        // Validate input
        if (empty(trim($cat_name))) {
            return false;
        }

        // Check if category name already exists for this user
        if ($this->category_exists($cat_name, $user_id)) {
            return false;
        }

        $sql = "INSERT INTO categories (cat_name, user_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            error_log("Category add failed - prepare error: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("si", $cat_name, $user_id);

        if ($stmt->execute()) {
            return $this->db->insert_id;
        } else {
            error_log("Category add failed - execute error: " . $stmt->error);
            return false;
        }
    }

    /**
     * Get all categories for a specific user
     * @param int $user_id User ID
     * @return array|false Returns array of categories or false on failure
     */
    public function get_categories_by_user(int $user_id)
    {
        $sql = "SELECT cat_id, cat_name, user_id FROM categories WHERE user_id = ? ORDER BY cat_name ASC";
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            error_log("Category fetch failed - prepare error: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            error_log("Category fetch failed - result error: " . $stmt->error);
            return false;
        }
    }

    /**
     * Get a specific category by ID for a user
     * @param int $cat_id Category ID
     * @param int $user_id User ID
     * @return array|false Returns category data or false on failure
     */
    public function get_category_by_id(int $cat_id, int $user_id)
    {
        $sql = "SELECT cat_id, cat_name, user_id FROM categories WHERE cat_id = ? AND user_id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            error_log("Category get by ID failed - prepare error: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("ii", $cat_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            return $result->fetch_assoc();
        } else {
            error_log("Category get by ID failed - result error: " . $stmt->error);
            return false;
        }
    }

    /**
     * Update a category name
     * @param int $cat_id Category ID
     * @param string $cat_name New category name
     * @param int $user_id User ID
     * @return bool Returns true on success, false on failure
     */
    public function update_category(int $cat_id, string $cat_name, int $user_id)
    {
        // Validate input
        if (empty(trim($cat_name))) {
            return false;
        }

        // Check if category exists and belongs to user
        $existing_category = $this->get_category_by_id($cat_id, $user_id);
        if (!$existing_category) {
            return false;
        }

        // Check if new category name already exists for this user (excluding current category)
        if ($this->category_exists($cat_name, $user_id, $cat_id)) {
            return false;
        }

        $sql = "UPDATE categories SET cat_name = ? WHERE cat_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            error_log("Category update failed - prepare error: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("sii", $cat_name, $cat_id, $user_id);

        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        } else {
            error_log("Category update failed - execute error: " . $stmt->error);
            return false;
        }
    }

    /**
     * Delete a category
     * @param int $cat_id Category ID
     * @param int $user_id User ID
     * @return bool Returns true on success, false on failure
     */
    public function delete_category(int $cat_id, int $user_id)
    {
        // Check if category exists and belongs to user
        $existing_category = $this->get_category_by_id($cat_id, $user_id);
        if (!$existing_category) {
            return false;
        }

        $sql = "DELETE FROM categories WHERE cat_id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            error_log("Category delete failed - prepare error: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("ii", $cat_id, $user_id);

        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        } else {
            error_log("Category delete failed - execute error: " . $stmt->error);
            return false;
        }
    }

    /**
     * Check if a category name already exists for a user
     * @param string $cat_name Category name
     * @param int $user_id User ID
     * @param int $exclude_cat_id Category ID to exclude (for updates)
     * @return bool Returns true if category exists, false otherwise
     */
    private function category_exists(string $cat_name, int $user_id, int $exclude_cat_id = 0)
    {
        if ($exclude_cat_id > 0) {
            $sql = "SELECT COUNT(*) as count FROM categories WHERE cat_name = ? AND user_id = ? AND cat_id != ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sii", $cat_name, $user_id, $exclude_cat_id);
        } else {
            $sql = "SELECT COUNT(*) as count FROM categories WHERE cat_name = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("si", $cat_name, $user_id);
        }

        if (!$stmt) {
            return false;
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }

    /**
     * Get total count of categories for a user
     * @param int $user_id User ID
     * @return int Returns count of categories
     */
    public function get_category_count(int $user_id)
    {
        $sql = "SELECT COUNT(*) as count FROM categories WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return (int)$row['count'];
    }
}

