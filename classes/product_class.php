<?php
// classes/product_class.php
require_once __DIR__ . '/../settings/db_class.php';
require_once __DIR__ . '/../settings/security.php';

class Product extends db_connection {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Add a new product
     * @param array $args - Contains product data
     * @return array - Success/failure response
     */
    public function add($args) {
        try {
            // Validate CSRF token
            if (!SecurityManager::validateCSRFToken($args['csrf_token'] ?? '')) {
                return array('success' => false, 'message' => 'Invalid security token');
            }
            
            $product_title = SecurityManager::sanitizeString($args['product_title']);
            $product_desc = SecurityManager::sanitizeString($args['product_description']);
            $product_price = floatval($args['product_price']);
            $product_keywords = SecurityManager::sanitizeString($args['product_keyword'] ?? '');
            $product_image = $args['product_image'] ?? null;
            $product_cat = SecurityManager::validateInteger($args['cat_id']);
            $product_brand = SecurityManager::validateInteger($args['brand_id']);
            
            // Validate input
            if (empty($product_title)) {
                return array('success' => false, 'message' => 'Product title is required');
            }
            
            if (empty($product_desc)) {
                return array('success' => false, 'message' => 'Product description is required');
            }
            
            if ($product_price <= 0) {
                return array('success' => false, 'message' => 'Product price must be greater than 0');
            }
            
            if (!$product_cat) {
                return array('success' => false, 'message' => 'Valid category is required');
            }
            
            if (!$product_brand) {
                return array('success' => false, 'message' => 'Valid brand is required');
            }
            
            // Insert new product
            $sql = "INSERT INTO products (product_title, product_desc, product_price, product_keywords, product_image, product_cat, product_brand) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('ssdssii', $product_title, $product_desc, $product_price, $product_keywords, $product_image, $product_cat, $product_brand);
            
            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Product added successfully', 'id' => $this->db->insert_id);
            } else {
                return array('success' => false, 'message' => 'Failed to add product: ' . $stmt->error);
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all products (public access)
     * @return array - Response with products
     */
    public function getAllProducts() {
        try {
            $sql = "SELECT p.product_id, p.product_title, p.product_desc, p.product_price, p.product_keywords, p.product_image,
                           c.cat_name, b.brand_name
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.product_id > 0
                    ORDER BY p.product_id DESC";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = array();
            while ($row = $result->fetch_assoc()) {
                // Generate secure token for each product
                $row['access_token'] = SecurityManager::generateProductToken($row['product_id']);
                // Remove sensitive internal IDs from public response
                unset($row['product_cat'], $row['product_brand']);
                $products[] = $row;
            }
            
            return array('success' => true, 'data' => $products, 'message' => 'Products retrieved successfully');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Get a product by secure token
     * @param string $token - Secure product token
     * @return array - Response with product data
     */
    public function getProductByToken($token) {
        try {
            $product_id = SecurityManager::validateProductToken($token);
            
            if (!$product_id) {
                return array('success' => false, 'message' => 'Invalid or expired product token');
            }
            
            $sql = "SELECT p.product_id, p.product_title, p.product_desc, p.product_price, p.product_keywords, p.product_image,
                           c.cat_name, b.brand_name
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.product_id = ?";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
                // Remove internal IDs from public response
                unset($product['product_cat'], $product['product_brand']);
                return array('success' => true, 'data' => $product);
            } else {
                return array('success' => false, 'message' => 'Product not found');
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Get a product by ID (admin only)
     * @param int $product_id - Product ID
     * @return array - Response with product data
     */
    public function getProductById($product_id) {
        try {
            $product_id = SecurityManager::validateInteger($product_id);
            
            if (!$product_id) {
                return array('success' => false, 'message' => 'Invalid product ID');
            }
            
            $sql = "SELECT p.product_id, p.product_title, p.product_desc, p.product_price, p.product_keywords, p.product_image,
                           c.cat_name, b.brand_name, p.product_cat, p.product_brand
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.product_id = ?";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return array('success' => true, 'data' => $result->fetch_assoc());
            } else {
                return array('success' => false, 'message' => 'Product not found');
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Update a product
     * @param array $args - Contains product data
     * @return array - Success/failure response
     */
    public function update($args) {
        try {
            // Validate CSRF token
            if (!SecurityManager::validateCSRFToken($args['csrf_token'] ?? '')) {
                return array('success' => false, 'message' => 'Invalid security token');
            }
            
            $product_id = SecurityManager::validateInteger($args['product_id']);
            $product_title = SecurityManager::sanitizeString($args['product_title']);
            $product_desc = SecurityManager::sanitizeString($args['product_description']);
            $product_price = floatval($args['product_price']);
            $product_keywords = SecurityManager::sanitizeString($args['product_keyword'] ?? '');
            $product_image = $args['product_image'] ?? null;
            $product_cat = SecurityManager::validateInteger($args['cat_id']);
            $product_brand = SecurityManager::validateInteger($args['brand_id']);
            
            // Validate input
            if (empty($product_title)) {
                return array('success' => false, 'message' => 'Product title is required');
            }
            
            if (empty($product_desc)) {
                return array('success' => false, 'message' => 'Product description is required');
            }
            
            if ($product_price <= 0) {
                return array('success' => false, 'message' => 'Product price must be greater than 0');
            }
            
            if (!$product_id) {
                return array('success' => false, 'message' => 'Invalid product ID');
            }
            
            // Check if product exists
            $existing = $this->getProductById($product_id);
            if (!$existing['success']) {
                return array('success' => false, 'message' => 'Product not found');
            }
            
            // Update product
            $sql = "UPDATE products SET 
                    product_title = ?, 
                    product_desc = ?, 
                    product_price = ?, 
                    product_keywords = ?, 
                    product_image = ?, 
                    product_cat = ?, 
                    product_brand = ?
                    WHERE product_id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('ssdssiii', $product_title, $product_desc, $product_price, $product_keywords, $product_image, $product_cat, $product_brand, $product_id);
            
            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Product updated successfully');
            } else {
                return array('success' => false, 'message' => 'Failed to update product: ' . $stmt->error);
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a product
     * @param int $product_id - Product ID
     * @return array - Success/failure response
     */
    public function delete($product_id) {
        try {
            $product_id = SecurityManager::validateInteger($product_id);
            
            if (!$product_id) {
                return array('success' => false, 'message' => 'Invalid product ID');
            }
            
            // Check if product exists
            $existing = $this->getProductById($product_id);
            if (!$existing['success']) {
                return array('success' => false, 'message' => 'Product not found');
            }
            
            // Delete product
            $sql = "DELETE FROM products WHERE product_id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('i', $product_id);
            
            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Product deleted successfully');
            } else {
                return array('success' => false, 'message' => 'Failed to delete product: ' . $stmt->error);
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Search products
     * @param string $query - Search query
     * @return array - Response with products
     */
    public function searchProducts($query) {
        try {
            $search_term = '%' . SecurityManager::sanitizeString($query, 100) . '%';
            
            $sql = "SELECT p.product_id, p.product_title, p.product_desc, p.product_price, p.product_keywords, p.product_image,
                           c.cat_name, b.brand_name
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.product_title LIKE ? OR p.product_desc LIKE ? OR p.product_keywords LIKE ?
                    ORDER BY p.product_id DESC";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('sss', $search_term, $search_term, $search_term);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = array();
            while ($row = $result->fetch_assoc()) {
                // Generate secure token for each product
                $row['access_token'] = SecurityManager::generateProductToken($row['product_id']);
                // Remove sensitive internal IDs from public response
                unset($row['product_cat'], $row['product_brand']);
                $products[] = $row;
            }
            
            return array('success' => true, 'data' => $products, 'message' => 'Search completed');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Filter products by category
     * @param int $cat_id - Category ID
     * @return array - Response with products
     */
    public function filterProductsByCategory($cat_id) {
        try {
            $cat_id = SecurityManager::validateInteger($cat_id);
            
            if (!$cat_id) {
                return array('success' => false, 'message' => 'Invalid category ID');
            }
            
            $sql = "SELECT p.product_id, p.product_title, p.product_desc, p.product_price, p.product_keywords, p.product_image,
                           c.cat_name, b.brand_name
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.product_cat = ?
                    ORDER BY p.product_id DESC";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('i', $cat_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = array();
            while ($row = $result->fetch_assoc()) {
                // Generate secure token for each product
                $row['access_token'] = SecurityManager::generateProductToken($row['product_id']);
                // Remove sensitive internal IDs from public response
                unset($row['product_cat'], $row['product_brand']);
                $products[] = $row;
            }
            
            return array('success' => true, 'data' => $products, 'message' => 'Products filtered by category');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Filter products by brand
     * @param int $brand_id - Brand ID
     * @return array - Response with products
     */
    public function filterProductsByBrand($brand_id) {
        try {
            $brand_id = SecurityManager::validateInteger($brand_id);
            
            if (!$brand_id) {
                return array('success' => false, 'message' => 'Invalid brand ID');
            }
            
            $sql = "SELECT p.product_id, p.product_title, p.product_desc, p.product_price, p.product_keywords, p.product_image,
                           c.cat_name, b.brand_name
                    FROM products p
                    LEFT JOIN categories c ON p.product_cat = c.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE p.product_brand = ?
                    ORDER BY p.product_id DESC";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('i', $brand_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = array();
            while ($row = $result->fetch_assoc()) {
                // Generate secure token for each product
                $row['access_token'] = SecurityManager::generateProductToken($row['product_id']);
                // Remove sensitive internal IDs from public response
                unset($row['product_cat'], $row['product_brand']);
                $products[] = $row;
            }
            
            return array('success' => true, 'data' => $products, 'message' => 'Products filtered by brand');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
}
?>