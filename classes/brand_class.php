<?php
// classes/brand_class.php
require_once __DIR__ . '/../settings/db_class.php';

class Brand extends db_connection {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Add a new brand
     * @param array $args - Contains brand data (brand_name, cat_id, user_id)
     * @return array - Success/failure response
     */
    public function add($args) {
        try {
            $brand_name = trim($args['brand_name']);
            $cat_id = intval($args['cat_id']);
            $user_id = intval($args['user_id']);
            
            // Validate input
            if (empty($brand_name)) {
                return array('success' => false, 'message' => 'Brand name is required');
            }
            
            if ($cat_id <= 0) {
                return array('success' => false, 'message' => 'Valid category is required');
            }
            
            // Check if brand+category combination already exists
            if ($this->brandExists($brand_name, $cat_id)) {
                return array('success' => false, 'message' => 'This brand already exists in this category');
            }
            
            // Insert new brand
            $sql = "INSERT INTO brands (brand_name, cat_id, user_id) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('sii', $brand_name, $cat_id, $user_id);
            
            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Brand added successfully', 'id' => $this->db->insert_id);
            } else {
                return array('success' => false, 'message' => 'Failed to add brand: ' . $stmt->error);
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all brands for a user, organized by category
     * @param int $user_id - User ID
     * @return array - Response with brands
     */
    public function getBrandsByUser($user_id) {
        try {
            $sql = "SELECT b.brand_id, b.brand_name, b.cat_id, c.cat_name 
                    FROM brands b 
                    JOIN categories c ON b.cat_id = c.cat_id 
                    WHERE b.user_id = ? 
                    ORDER BY c.cat_name, b.brand_name";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $brands = array();
            while ($row = $result->fetch_assoc()) {
                $brands[] = $row;
            }
            
            return array('success' => true, 'data' => $brands, 'message' => 'Brands retrieved successfully');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Get a brand by ID
     * @param int $brand_id - Brand ID
     * @param int $user_id - User ID
     * @return array - Response with brand data
     */
    public function getBrandById($brand_id, $user_id) {
        try {
            $sql = "SELECT b.brand_id, b.brand_name, b.cat_id, c.cat_name 
                    FROM brands b 
                    JOIN categories c ON b.cat_id = c.cat_id 
                    WHERE b.brand_id = ? AND b.user_id = ?";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('ii', $brand_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return array('success' => true, 'data' => $result->fetch_assoc());
            } else {
                return array('success' => false, 'message' => 'Brand not found');
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Update a brand
     * @param array $args - Contains brand data (brand_id, brand_name, cat_id, user_id)
     * @return array - Success/failure response
     */
    public function update($args) {
        try {
            $brand_id = intval($args['brand_id']);
            $brand_name = trim($args['brand_name']);
            $cat_id = intval($args['cat_id']);
            $user_id = intval($args['user_id']);
            
            // Validate input
            if (empty($brand_name)) {
                return array('success' => false, 'message' => 'Brand name is required');
            }
            
            if ($brand_id <= 0) {
                return array('success' => false, 'message' => 'Invalid brand ID');
            }
            
            // Check if brand exists and belongs to user
            $existing = $this->getBrandById($brand_id, $user_id);
            if (!$existing['success']) {
                return array('success' => false, 'message' => 'Brand not found or access denied');
            }
            
            // Check if new brand+category combination already exists (excluding current brand)
            $sql_check = "SELECT brand_id FROM brands WHERE brand_name = ? AND cat_id = ? AND user_id = ? AND brand_id != ?";
            $stmt_check = $this->db->prepare($sql_check);
            $stmt_check->bind_param('siii', $brand_name, $cat_id, $user_id, $brand_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                return array('success' => false, 'message' => 'This brand already exists in this category');
            }
            
            // Update brand
            $sql = "UPDATE brands SET brand_name = ?, cat_id = ? WHERE brand_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('siii', $brand_name, $cat_id, $brand_id, $user_id);
            
            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Brand updated successfully');
            } else {
                return array('success' => false, 'message' => 'Failed to update brand: ' . $stmt->error);
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a brand
     * @param int $brand_id - Brand ID
     * @param int $user_id - User ID
     * @return array - Success/failure response
     */
    public function delete($brand_id, $user_id) {
        try {
            $brand_id = intval($brand_id);
            
            if ($brand_id <= 0) {
                return array('success' => false, 'message' => 'Invalid brand ID');
            }
            
            // Check if brand exists and belongs to user
            $existing = $this->getBrandById($brand_id, $user_id);
            if (!$existing['success']) {
                return array('success' => false, 'message' => 'Brand not found or access denied');
            }
            
            // Check if brand is used by any products
            $sql_check = "SELECT COUNT(*) as count FROM products WHERE brand_id = ?";
            $stmt_check = $this->db->prepare($sql_check);
            $stmt_check->bind_param('i', $brand_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            $count_check = $result_check->fetch_assoc();
            
            if ($count_check['count'] > 0) {
                return array('success' => false, 'message' => 'Cannot delete brand. It is used by ' . $count_check['count'] . ' product(s)');
            }
            
            // Delete brand
            $sql = "DELETE FROM brands WHERE brand_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('ii', $brand_id, $user_id);
            
            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Brand deleted successfully');
            } else {
                return array('success' => false, 'message' => 'Failed to delete brand: ' . $stmt->error);
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if a brand exists in a category
     * @param string $brand_name - Brand name
     * @param int $cat_id - Category ID
     * @return bool
     */
    private function brandExists($brand_name, $cat_id) {
        $sql = "SELECT brand_id FROM brands WHERE brand_name = ? AND cat_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('si', $brand_name, $cat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    /**
     * Get all brands (customer-facing)
     * @return array - Response with all brands
     */
    public function getAllBrands() {
        try {
            $sql = "SELECT b.brand_id, b.brand_name, b.cat_id, c.cat_name 
                    FROM brands b 
                    JOIN categories c ON b.cat_id = c.cat_id 
                    ORDER BY c.cat_name, b.brand_name";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $brands = array();
            while ($row = $result->fetch_assoc()) {
                $brands[] = $row;
            }
            
            return array('success' => true, 'data' => $brands, 'message' => 'All brands retrieved successfully');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
}
