<?php
// classes/brand_class.php
require_once __DIR__ . '/../settings/db_class.php';

class Brand extends db_connection {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Add a new brand
     * @param array $args - Contains brand data (brand_name)
     * @return array - Success/failure response
     */
    public function add($args) {
        try {
            $brand_name = trim($args['brand_name']);
            
            // Validate input
            if (empty($brand_name)) {
                return array('success' => false, 'message' => 'Brand name is required');
            }
            
            // Check if brand already exists
            if ($this->brandExists($brand_name)) {
                return array('success' => false, 'message' => 'This brand already exists');
            }
            
            // Insert new brand
            $sql = "INSERT INTO brands (brand_name) VALUES (?)";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('s', $brand_name);
            
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
     * Get all brands
     * @return array - Response with brands
     */
    public function getAllBrands() {
        try {
            $sql = "SELECT brand_id, brand_name FROM brands ORDER BY brand_name";
            
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
            
            return array('success' => true, 'data' => $brands, 'message' => 'Brands retrieved successfully');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Get a brand by ID
     * @param int $brand_id - Brand ID
     * @return array - Response with brand data
     */
    public function getBrandById($brand_id) {
        try {
            $sql = "SELECT brand_id, brand_name FROM brands WHERE brand_id = ?";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('i', $brand_id);
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
     * @param array $args - Contains brand data (brand_id, brand_name)
     * @return array - Success/failure response
     */
    public function update($args) {
        try {
            $brand_id = intval($args['brand_id']);
            $brand_name = trim($args['brand_name']);
            
            // Validate input
            if (empty($brand_name)) {
                return array('success' => false, 'message' => 'Brand name is required');
            }
            
            if ($brand_id <= 0) {
                return array('success' => false, 'message' => 'Invalid brand ID');
            }
            
            // Check if brand exists
            $existing = $this->getBrandById($brand_id);
            if (!$existing['success']) {
                return array('success' => false, 'message' => 'Brand not found');
            }
            
            // Check if new brand name already exists (excluding current brand)
            $sql_check = "SELECT brand_id FROM brands WHERE brand_name = ? AND brand_id != ?";
            $stmt_check = $this->db->prepare($sql_check);
            $stmt_check->bind_param('si', $brand_name, $brand_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                return array('success' => false, 'message' => 'Brand name already exists');
            }
            
            // Update brand
            $sql = "UPDATE brands SET brand_name = ? WHERE brand_id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('si', $brand_name, $brand_id);
            
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
     * @return array - Success/failure response
     */
    public function delete($brand_id) {
        try {
            $brand_id = intval($brand_id);
            
            if ($brand_id <= 0) {
                return array('success' => false, 'message' => 'Invalid brand ID');
            }
            
            // Check if brand exists
            $existing = $this->getBrandById($brand_id);
            if (!$existing['success']) {
                return array('success' => false, 'message' => 'Brand not found');
            }
            
            // Delete brand
            $sql = "DELETE FROM brands WHERE brand_id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('i', $brand_id);
            
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
     * Check if brand exists
     * @param string $brand_name - Brand name
     * @return bool - True if exists, false otherwise
     */
    private function brandExists($brand_name) {
        $sql = "SELECT brand_id FROM brands WHERE brand_name = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $brand_name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0;
    }
}
?>