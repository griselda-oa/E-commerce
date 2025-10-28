<?php
// classes/product_class.php
require_once __DIR__ . '/../settings/db_class.php';

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
            $product_title = trim($args['product_title']);
            $product_description = trim($args['product_description']);
            $product_price = floatval($args['product_price']);
            $product_keyword = trim($args['product_keyword'] ?? '');
            $product_image = $args['product_image'] ?? null;
            $cat_id = intval($args['cat_id']);
            $brand_id = intval($args['brand_id']);
            $user_id = intval($args['user_id']);
            
            // Validate input
            if (empty($product_title)) {
                return array('success' => false, 'message' => 'Product title is required');
            }
            
            if (empty($product_description)) {
                return array('success' => false, 'message' => 'Product description is required');
            }
            
            if ($product_price <= 0) {
                return array('success' => false, 'message' => 'Product price must be greater than 0');
            }
            
            if ($cat_id <= 0) {
                return array('success' => false, 'message' => 'Valid category is required');
            }
            
            if ($brand_id <= 0) {
                return array('success' => false, 'message' => 'Valid brand is required');
            }
            
            // Insert new product
            $sql = "INSERT INTO products (product_title, product_description, product_price, product_keyword, product_image, cat_id, brand_id, user_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('ssdsssii', $product_title, $product_description, $product_price, $product_keyword, $product_image, $cat_id, $brand_id, $user_id);
            
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
     * Update a product
     * @param array $args - Contains product data
     * @return array - Success/failure response
     */
    public function update($args) {
        try {
            $product_id = intval($args['product_id']);
            $product_title = trim($args['product_title']);
            $product_description = trim($args['product_description']);
            $product_price = floatval($args['product_price']);
            $product_keyword = trim($args['product_keyword'] ?? '');
            $product_image = $args['product_image'] ?? null;
            $cat_id = intval($args['cat_id']);
            $brand_id = intval($args['brand_id']);
            $user_id = intval($args['user_id']);
            
            // Validate input
            if (empty($product_title)) {
                return array('success' => false, 'message' => 'Product title is required');
            }
            
            if (empty($product_description)) {
                return array('success' => false, 'message' => 'Product description is required');
            }
            
            if ($product_price <= 0) {
                return array('success' => false, 'message' => 'Product price must be greater than 0');
            }
            
            if ($product_id <= 0) {
                return array('success' => false, 'message' => 'Invalid product ID');
            }
            
            // Check if product exists and belongs to user
            $existing = $this->getProductById($product_id, $user_id);
            if (!$existing['success']) {
                return array('success' => false, 'message' => 'Product not found or access denied');
            }
            
            // Update product
            if ($product_image) {
                $sql = "UPDATE products SET product_title = ?, product_description = ?, product_price = ?, product_keyword = ?, product_image = ?, cat_id = ?, brand_id = ? WHERE product_id = ? AND user_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('ssdssiiii', $product_title, $product_description, $product_price, $product_keyword, $product_image, $cat_id, $brand_id, $product_id, $user_id);
            } else {
                $sql = "UPDATE products SET product_title = ?, product_description = ?, product_price = ?, product_keyword = ?, cat_id = ?, brand_id = ? WHERE product_id = ? AND user_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('ssdsiiii', $product_title, $product_description, $product_price, $product_keyword, $cat_id, $brand_id, $product_id, $user_id);
            }
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
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
     * Get all products for a user, organized by category and brand
     * @param int $user_id - User ID
     * @return array - Response with products
     */
    public function getProductsByUser($user_id) {
        try {
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p 
                    JOIN categories c ON p.cat_id = c.cat_id 
                    JOIN brands b ON p.brand_id = b.brand_id 
                    WHERE p.user_id = ? 
                    ORDER BY c.cat_name, b.brand_name, p.product_title";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = array();
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            return array('success' => true, 'data' => $products, 'message' => 'Products retrieved successfully');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Get a product by ID
     * @param int $product_id - Product ID
     * @param int $user_id - User ID (0 for customer view)
     * @return array - Response with product data
     */
    public function getProductById($product_id, $user_id) {
        try {
            if ($user_id > 0) {
                // Admin view - only their products
                $sql = "SELECT p.*, c.cat_name, b.brand_name 
                        FROM products p 
                        JOIN categories c ON p.cat_id = c.cat_id 
                        JOIN brands b ON p.brand_id = b.brand_id 
                        WHERE p.product_id = ? AND p.user_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('ii', $product_id, $user_id);
            } else {
                // Customer view - any product
                $sql = "SELECT p.*, c.cat_name, b.brand_name 
                        FROM products p 
                        JOIN categories c ON p.cat_id = c.cat_id 
                        JOIN brands b ON p.brand_id = b.brand_id 
                        WHERE p.product_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('i', $product_id);
            }
            
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
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
     * View all products (customer-facing)
     * @return array - Response with all products
     */
    public function view_all_products() {
        try {
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p 
                    JOIN categories c ON p.cat_id = c.cat_id 
                    JOIN brands b ON p.brand_id = b.brand_id 
                    ORDER BY c.cat_name, b.brand_name, p.product_title";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = array();
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            return array('success' => true, 'data' => $products, 'message' => 'All products retrieved successfully');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Search products by query
     * @param string $query - Search query
     * @return array - Response with search results
     */
    public function search_products($query) {
        try {
            $searchTerm = '%' . $query . '%';
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p 
                    JOIN categories c ON p.cat_id = c.cat_id 
                    JOIN brands b ON p.brand_id = b.brand_id 
                    WHERE p.product_title LIKE ? 
                       OR p.product_description LIKE ? 
                       OR p.product_keyword LIKE ?
                    ORDER BY c.cat_name, b.brand_name, p.product_title";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = array();
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            return array('success' => true, 'data' => $products, 'message' => 'Search completed successfully');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Filter products by category
     * @param int $cat_id - Category ID
     * @return array - Response with filtered products
     */
    public function filter_products_by_category($cat_id) {
        try {
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p 
                    JOIN categories c ON p.cat_id = c.cat_id 
                    JOIN brands b ON p.brand_id = b.brand_id 
                    WHERE p.cat_id = ?
                    ORDER BY b.brand_name, p.product_title";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('i', $cat_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = array();
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            return array('success' => true, 'data' => $products, 'message' => 'Products filtered by category successfully');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Filter products by brand
     * @param int $brand_id - Brand ID
     * @return array - Response with filtered products
     */
    public function filter_products_by_brand($brand_id) {
        try {
            $sql = "SELECT p.*, c.cat_name, b.brand_name 
                    FROM products p 
                    JOIN categories c ON p.cat_id = c.cat_id 
                    JOIN brands b ON p.brand_id = b.brand_id 
                    WHERE p.brand_id = ?
                    ORDER BY c.cat_name, p.product_title";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            $stmt->bind_param('i', $brand_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = array();
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            return array('success' => true, 'data' => $products, 'message' => 'Products filtered by brand successfully');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * View single product (customer-facing)
     * @param int $id - Product ID
     * @return array - Response with product data
     */
    public function view_single_product($id) {
        return $this->getProductById($id, 0);
    }
}
?>
