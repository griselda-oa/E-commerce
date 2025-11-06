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
            
            // Insert new product - use correct column names: product_description, product_keyword, cat_id, brand_id
            $sql = "INSERT INTO products (product_title, product_description, product_price, product_keyword, product_image, cat_id, brand_id) 
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
            // Query matching actual database schema: product_description, product_keyword, cat_id, brand_id
            $sql = "SELECT 
                        p.product_id, 
                        p.product_title, 
                        p.product_description as product_desc, 
                        p.product_price, 
                        p.product_keyword as product_keywords, 
                        p.product_image,
                        p.cat_id,
                        p.brand_id,
                        c.cat_name, 
                        b.brand_name
                    FROM products p
                    LEFT JOIN categories c ON p.cat_id = c.cat_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
                    ORDER BY p.product_id DESC";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'SQL Error: ' . $this->db->error . ' | SQL: ' . $sql);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            if (!$result) {
                return array('success' => false, 'message' => 'Query execution failed: ' . $this->db->error);
            }
            
            $products = array();
            while ($row = $result->fetch_assoc()) {
                // Generate secure token if not present
                if (empty($row['product_token'])) {
                    $row['product_token'] = SecurityManager::generateProductToken($row['product_id']);
                }
                // Ensure all fields exist
                $row['cat_name'] = $row['cat_name'] ?? 'Uncategorized';
                $row['brand_name'] = $row['brand_name'] ?? 'No Brand';
                $row['product_desc'] = $row['product_desc'] ?? '';
                $row['product_keywords'] = $row['product_keywords'] ?? '';
                $row['product_image'] = $row['product_image'] ?? null;
                // Add alias for consistency
                $row['product_description'] = $row['product_desc'];
                $products[] = $row;
            }
            
            return array('success' => true, 'data' => $products, 'message' => 'Products retrieved successfully');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        } catch (Error $e) {
            return array('success' => false, 'message' => 'Fatal Error: ' . $e->getMessage());
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
            
            $sql = "SELECT p.product_id, p.product_title, p.product_description as product_desc, p.product_price, p.product_keyword as product_keywords, p.product_image,
                           c.cat_name, b.brand_name
                    FROM products p
                    LEFT JOIN categories c ON p.cat_id = c.cat_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
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
            
            $sql = "SELECT p.product_id, p.product_title, p.product_description as product_desc, p.product_price, p.product_keyword as product_keywords, p.product_image,
                           c.cat_name, b.brand_name, p.cat_id, p.brand_id
                    FROM products p
                    LEFT JOIN categories c ON p.cat_id = c.cat_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
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
                // Add aliases for consistency with admin form
                $product['product_description'] = $product['product_desc'];
                $product['product_keywords'] = $product['product_keywords'] ?? '';
                return array('success' => true, 'data' => $product);
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
            
            // Update product - use correct column names: product_description, product_keyword, cat_id, brand_id
            $sql = "UPDATE products SET 
                    product_title = ?, 
                    product_description = ?, 
                    product_price = ?, 
                    product_keyword = ?, 
                    product_image = ?, 
                    cat_id = ?, 
                    brand_id = ?
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
            
            $sql = "SELECT p.product_id, p.product_title, p.product_description as product_desc, p.product_price, p.product_keyword as product_keywords, p.product_image,
                           c.cat_name, b.brand_name
                    FROM products p
                    LEFT JOIN categories c ON p.cat_id = c.cat_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
                    WHERE p.product_title LIKE ? OR p.product_description LIKE ? OR p.product_keyword LIKE ?
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
                // Remove sensitive internal IDs from public response (if they exist)
                unset($row['cat_id'], $row['brand_id']);
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
            
            $sql = "SELECT p.product_id, p.product_title, p.product_description as product_desc, p.product_price, p.product_keyword as product_keywords, p.product_image,
                           c.cat_name, b.brand_name
                    FROM products p
                    LEFT JOIN categories c ON p.cat_id = c.cat_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
                    WHERE p.cat_id = ?
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
                // Remove sensitive internal IDs from public response (if they exist)
                unset($row['cat_id'], $row['brand_id']);
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
            
            $sql = "SELECT p.product_id, p.product_title, p.product_description as product_desc, p.product_price, p.product_keyword as product_keywords, p.product_image,
                           c.cat_name, b.brand_name
                    FROM products p
                    LEFT JOIN categories c ON p.cat_id = c.cat_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
                    WHERE p.brand_id = ?
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
                // Remove sensitive internal IDs from public response (if they exist)
                unset($row['cat_id'], $row['brand_id']);
                $products[] = $row;
            }
            
            return array('success' => true, 'data' => $products, 'message' => 'Products filtered by brand');
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Efficient composite search - combines keyword, category, brand, and price filters
     * Uses optimized SQL for fast performance
     * @param array $filters - Array with: keyword (string), cat_id (int), brand_id (int), min_price (float), max_price (float)
     * @return array - Response with products
     */
    public function compositeSearch($filters) {
        try {
            $where_conditions = array();
            $params = array();
            $types = '';
            
            // Keyword search - efficient LIKE with multiple fields
            if (!empty($filters['keyword'])) {
                $keyword = SecurityManager::sanitizeString($filters['keyword'], 100);
                $search_term = '%' . $keyword . '%';
                $where_conditions[] = "(p.product_title LIKE ? OR p.product_description LIKE ? OR p.product_keyword LIKE ?)";
                $params[] = $search_term;
                $params[] = $search_term;
                $params[] = $search_term;
                $types .= 'sss';
            }
            
            // Category filter
            if (!empty($filters['cat_id']) && $filters['cat_id'] > 0) {
                $cat_id = SecurityManager::validateInteger($filters['cat_id']);
                if ($cat_id) {
                    $where_conditions[] = "p.cat_id = ?";
                    $params[] = $cat_id;
                    $types .= 'i';
                }
            }
            
            // Brand filter
            if (!empty($filters['brand_id']) && $filters['brand_id'] > 0) {
                $brand_id = SecurityManager::validateInteger($filters['brand_id']);
                if ($brand_id) {
                    $where_conditions[] = "p.brand_id = ?";
                    $params[] = $brand_id;
                    $types .= 'i';
                }
            }
            
            // Price range filter
            if (!empty($filters['min_price']) && $filters['min_price'] > 0) {
                $min_price = SecurityManager::validateFloat($filters['min_price']);
                if ($min_price !== false) {
                    $where_conditions[] = "p.product_price >= ?";
                    $params[] = $min_price;
                    $types .= 'd';
                }
            }
            
            if (!empty($filters['max_price']) && $filters['max_price'] > 0) {
                $max_price = SecurityManager::validateFloat($filters['max_price']);
                if ($max_price !== false) {
                    $where_conditions[] = "p.product_price <= ?";
                    $params[] = $max_price;
                    $types .= 'd';
                }
            }
            
            // Build SQL query - use correct column names
            $sql = "SELECT p.product_id, p.product_title, p.product_description as product_desc, p.product_price, p.product_keyword as product_keywords, p.product_image,
                           c.cat_name, b.brand_name
                    FROM products p
                    LEFT JOIN categories c ON p.cat_id = c.cat_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id";
            
            if (!empty($where_conditions)) {
                $sql .= " WHERE " . implode(' AND ', $where_conditions);
            }
            
            $sql .= " ORDER BY p.product_id DESC LIMIT 500";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }
            
            // Bind parameters dynamically
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            if (!$result) {
                return array('success' => false, 'message' => 'Query execution failed: ' . $this->db->error);
            }
            
            $products = array();
            while ($row = $result->fetch_assoc()) {
                // Generate secure token for each product if not present
                if (empty($row['product_token'])) {
                    $row['product_token'] = SecurityManager::generateProductToken($row['product_id']);
                }
                // Ensure all fields exist
                $row['cat_name'] = $row['cat_name'] ?? 'Uncategorized';
                $row['brand_name'] = $row['brand_name'] ?? 'No Brand';
                $row['product_desc'] = $row['product_desc'] ?? '';
                $row['product_keywords'] = $row['product_keywords'] ?? '';
                $row['product_image'] = $row['product_image'] ?? null;
                // Add alias for consistency
                $row['product_description'] = $row['product_desc'];
                // Remove sensitive internal IDs from public response (if they exist)
                unset($row['cat_id'], $row['brand_id']);
                $products[] = $row;
            }
            
            return array('success' => true, 'data' => $products, 'message' => 'Composite search completed', 'count' => count($products));
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a product
     * @param int $product_id - Product ID to delete
     * @return array - Success/failure response
     */
    public function delete($product_id) {
        try {
            $product_id = SecurityManager::validateInteger($product_id);
            
            if (!$product_id) {
                return array('success' => false, 'message' => 'Invalid product ID');
            }
            
            // First, get product info to delete associated images
            $sql = "SELECT product_image FROM products WHERE product_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
                
                // Delete the product from database
                $sql = "DELETE FROM products WHERE product_id = ?";
                $stmt = $this->db->prepare($sql);
                
                if (!$stmt) {
                    return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
                }
                
                $stmt->bind_param('i', $product_id);
                
                if ($stmt->execute()) {
                    // Optionally delete associated images from uploads folder
                    // (We'll keep images for now, but they can be cleaned up later)
                    
                    return array('success' => true, 'message' => 'Product deleted successfully');
                } else {
                    return array('success' => false, 'message' => 'Failed to delete product: ' . $stmt->error);
                }
            } else {
                return array('success' => false, 'message' => 'Product not found');
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }
}
?>