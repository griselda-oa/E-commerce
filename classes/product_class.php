<?php
require_once __DIR__ . '/../settings/db_class.php';

class Product extends db_connection
{
    /**
     * Add a new product
     * @param string $product_name
     * @param string $product_description
     * @param float $product_price
     * @param int $product_stock
     * @param int $cat_id
     * @param int $user_id
     * @param string $product_image
     * @return array
     */
    public function add_product(string $product_name, string $product_description, float $product_price, int $product_stock, int $cat_id, int $user_id, string $product_image = null): array
    {
        try {
            // Check if product name already exists
            $check_sql = "SELECT product_id FROM products WHERE product_title = ?";
            $check_stmt = $this->db->prepare($check_sql);
            $check_stmt->bind_param("s", $product_name);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Product name already exists'
                ];
            }
            
            // Insert new product
            $sql = "INSERT INTO products (product_title, product_desc, product_price, product_cat, product_brand, product_image, product_keywords) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $product_keywords = $product_name . ' ' . $product_description; // Simple keywords
            $stmt->bind_param("ssdiis", $product_name, $product_description, $product_price, $cat_id, $user_id, $product_image, $product_keywords);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Product added successfully',
                    'product_id' => $this->db->insert_id
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to add product: ' . $stmt->error
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error adding product: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get a single product by ID
     * @param int $product_id
     * @return array
     */
    public function get_product(int $product_id): array
    {
        try {
            $sql = "SELECT p.*, c.cat_name FROM products p 
                    LEFT JOIN categories c ON p.product_cat = c.cat_id 
                    WHERE p.product_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return [
                    'success' => true,
                    'data' => $result->fetch_assoc()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Product not found'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error fetching product: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all products created by a specific user
     * @param int $user_id
     * @return array
     */
    public function get_products_by_user(int $user_id): array
    {
        try {
            $sql = "SELECT p.*, c.cat_name FROM products p 
                    LEFT JOIN categories c ON p.product_cat = c.cat_id 
                    WHERE p.product_brand = ? 
                    ORDER BY p.product_id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            return [
                'success' => true,
                'data' => $products
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error fetching products: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all products (for public view)
     * @return array
     */
    public function get_all_products(): array
    {
        try {
            $sql = "SELECT p.*, c.cat_name FROM products p 
                    LEFT JOIN categories c ON p.product_cat = c.cat_id 
                    ORDER BY p.product_id DESC";
            $result = $this->db_query($sql);
            
            if ($result) {
                $products = $this->results->fetch_all(MYSQLI_ASSOC);
                return [
                    'success' => true,
                    'data' => $products
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error fetching products'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error fetching products: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update a product
     * @param int $product_id
     * @param string $product_name
     * @param string $product_description
     * @param float $product_price
     * @param int $cat_id
     * @param int $user_id
     * @param string $product_image
     * @return array
     */
    public function update_product(int $product_id, string $product_name, string $product_description, float $product_price, int $cat_id, int $user_id, string $product_image = null): array
    {
        try {
            // Check if product exists and belongs to user
            $check_sql = "SELECT product_id FROM products WHERE product_id = ? AND product_brand = ?";
            $check_stmt = $this->db->prepare($check_sql);
            $check_stmt->bind_param("ii", $product_id, $user_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows === 0) {
                return [
                    'success' => false,
                    'message' => 'Product not found or access denied'
                ];
            }
            
            // Check if product name already exists (excluding current product)
            $name_check_sql = "SELECT product_id FROM products WHERE product_title = ? AND product_id != ?";
            $name_check_stmt = $this->db->prepare($name_check_sql);
            $name_check_stmt->bind_param("si", $product_name, $product_id);
            $name_check_stmt->execute();
            $name_result = $name_check_stmt->get_result();
            
            if ($name_result->num_rows > 0) {
                return [
                    'success' => false,
                    'message' => 'Product name already exists'
                ];
            }
            
            // Update product
            $sql = "UPDATE products SET product_title = ?, product_desc = ?, product_price = ?, product_cat = ?, product_image = ?, product_keywords = ? WHERE product_id = ? AND product_brand = ?";
            $stmt = $this->db->prepare($sql);
            $product_keywords = $product_name . ' ' . $product_description; // Simple keywords
            $stmt->bind_param("ssdiissi", $product_name, $product_description, $product_price, $cat_id, $product_image, $product_keywords, $product_id, $user_id);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Product updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update product: ' . $stmt->error
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a product
     * @param int $product_id
     * @param int $user_id
     * @return array
     */
    public function delete_product(int $product_id, int $user_id): array
    {
        try {
            // Check if product exists and belongs to user
            $check_sql = "SELECT product_id FROM products WHERE product_id = ? AND product_brand = ?";
            $check_stmt = $this->db->prepare($check_sql);
            $check_stmt->bind_param("ii", $product_id, $user_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows === 0) {
                return [
                    'success' => false,
                    'message' => 'Product not found or access denied'
                ];
            }
            
            // Delete product
            $sql = "DELETE FROM products WHERE product_id = ? AND product_brand = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ii", $product_id, $user_id);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Product deleted successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to delete product: ' . $stmt->error
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get products by category
     * @param int $cat_id
     * @return array
     */
    public function get_products_by_category(int $cat_id): array
    {
        try {
            $sql = "SELECT p.*, c.cat_name FROM products p 
                    LEFT JOIN categories c ON p.product_cat = c.cat_id 
                    WHERE p.product_cat = ? 
                    ORDER BY p.product_id DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $cat_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            return [
                'success' => true,
                'data' => $products
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error fetching products by category: ' . $e->getMessage()
            ];
        }
    }
}
?>