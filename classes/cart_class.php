<?php
// classes/cart_class.php
require_once __DIR__ . '/../settings/db_class.php';
require_once __DIR__ . '/../settings/security.php';

class Cart extends db_connection
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add a product to the cart
     * If product already exists, update quantity instead of duplicating
     * @param array $args - Contains customer_id, product_id, quantity
     * @return array - Success/failure response
     */
    public function add($args)
    {
        try {
            $customer_id = SecurityManager::validateInteger($args['customer_id'] ?? 0);
            $product_id = SecurityManager::validateInteger($args['product_id'] ?? 0);
            $quantity = SecurityManager::validateInteger($args['quantity'] ?? 1);

            if (!$customer_id) {
                return array('success' => false, 'message' => 'Valid customer ID is required');
            }

            if (!$product_id) {
                return array('success' => false, 'message' => 'Valid product ID is required');
            }

            if ($quantity <= 0) {
                return array('success' => false, 'message' => 'Quantity must be greater than 0');
            }

            // Check if product already exists in cart
            $existing = $this->checkProductExists($customer_id, $product_id);
            
            if ($existing) {
                // Update quantity instead of creating duplicate
                $new_quantity = $existing['qty'] + $quantity;
                return $this->updateQuantity($existing['p_id'], $new_quantity, $customer_id);
            }

            // Insert new cart item - using actual column names: c_id, p_id, qty
            $sql = "INSERT INTO cart (c_id, p_id, qty) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }

            $stmt->bind_param('iii', $customer_id, $product_id, $quantity);

            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Product added to cart successfully', 'p_id' => $this->db->insert_id);
            } else {
                return array('success' => false, 'message' => 'Failed to add product to cart: ' . $stmt->error);
            }

        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update quantity of a cart item
     * @param int $cart_id - Product ID (p_id) in cart
     * @param int $quantity - New quantity
     * @param int $customer_id - Customer ID for security
     * @return array - Success/failure response
     */
    public function updateQuantity($cart_id, $quantity, $customer_id = null)
    {
        try {
            $cart_id = SecurityManager::validateInteger($cart_id);
            $quantity = SecurityManager::validateInteger($quantity);

            if (!$cart_id) {
                return array('success' => false, 'message' => 'Valid cart ID is required');
            }

            if ($quantity <= 0) {
                return array('success' => false, 'message' => 'Quantity must be greater than 0');
            }

            // Use both c_id and p_id for safety (p_id is product_id, cart_id is actually p_id)
            if ($customer_id) {
                $customer_id = SecurityManager::validateInteger($customer_id);
                $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?";
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
                }
                $stmt->bind_param('iii', $quantity, $cart_id, $customer_id);
            } else {
                // Fallback: use only p_id (less secure but works if unique constraint exists)
                $sql = "UPDATE cart SET qty = ? WHERE p_id = ?";
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
                }
                $stmt->bind_param('ii', $quantity, $cart_id);
            }

            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Cart updated successfully');
            } else {
                return array('success' => false, 'message' => 'Failed to update cart: ' . $stmt->error);
            }

        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove a product from the cart
     * @param int $cart_id - Product ID (p_id) in cart
     * @param int $customer_id - Customer ID for security
     * @return array - Success/failure response
     */
    public function remove($cart_id, $customer_id = null)
    {
        try {
            $cart_id = SecurityManager::validateInteger($cart_id);

            if (!$cart_id) {
                return array('success' => false, 'message' => 'Valid cart ID is required');
            }

            // Use both c_id and p_id for safety
            if ($customer_id) {
                $customer_id = SecurityManager::validateInteger($customer_id);
                $sql = "DELETE FROM cart WHERE p_id = ? AND c_id = ?";
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
                }
                $stmt->bind_param('ii', $cart_id, $customer_id);
            } else {
                // Fallback: use only p_id
                $sql = "DELETE FROM cart WHERE p_id = ?";
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
                }
                $stmt->bind_param('i', $cart_id);
            }

            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Product removed from cart successfully');
            } else {
                return array('success' => false, 'message' => 'Failed to remove product from cart: ' . $stmt->error);
            }

        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Get all cart items for a customer with product details
     * @param int $customer_id - Customer ID
     * @return array - Response with cart items
     */
    public function getCart($customer_id)
    {
        try {
            $customer_id = SecurityManager::validateInteger($customer_id);

            if (!$customer_id) {
                return array('success' => false, 'message' => 'Valid customer ID is required', 'data' => []);
            }

            // Using actual column names: p_id (product_id), c_id (customer_id), qty (quantity)
            // Note: p_id serves as both product_id and we'll use it as cart identifier
            $sql = "SELECT 
                        c.p_id as cart_id,
                        c.c_id as customer_id,
                        c.p_id as product_id,
                        c.qty as quantity,
                        p.product_title,
                        p.product_desc as product_description,
                        p.product_price,
                        p.product_image,
                        cat.cat_name,
                        b.brand_name
                    FROM cart c
                    INNER JOIN products p ON c.p_id = p.product_id
                    LEFT JOIN categories cat ON p.product_cat = cat.cat_id
                    LEFT JOIN brands b ON p.product_brand = b.brand_id
                    WHERE c.c_id = ?";

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error, 'data' => []);
            }

            $stmt->bind_param('i', $customer_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result) {
                return array('success' => false, 'message' => 'Query execution failed: ' . $this->db->error, 'data' => []);
            }

            $cart_items = array();
            $total = 0;

            while ($row = $result->fetch_assoc()) {
                $subtotal = floatval($row['product_price']) * intval($row['quantity']);
                $row['subtotal'] = $subtotal;
                $total += $subtotal;
                $cart_items[] = $row;
            }

            return array(
                'success' => true,
                'message' => 'Cart retrieved successfully',
                'data' => $cart_items,
                'total' => $total,
                'item_count' => count($cart_items)
            );

        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage(), 'data' => []);
        }
    }

    /**
     * Empty the cart for a customer
     * @param int $customer_id - Customer ID
     * @return array - Success/failure response
     */
    public function emptyCart($customer_id)
    {
        try {
            $customer_id = SecurityManager::validateInteger($customer_id);

            if (!$customer_id) {
                return array('success' => false, 'message' => 'Valid customer ID is required');
            }

            $sql = "DELETE FROM cart WHERE c_id = ?";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }

            $stmt->bind_param('i', $customer_id);

            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Cart emptied successfully');
            } else {
                return array('success' => false, 'message' => 'Failed to empty cart: ' . $stmt->error);
            }

        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Check if a product already exists in the cart
     * @param int $customer_id - Customer ID
     * @param int $product_id - Product ID
     * @return array|false - Cart item data if exists, false otherwise
     */
    public function checkProductExists($customer_id, $product_id)
    {
        try {
            $customer_id = SecurityManager::validateInteger($customer_id);
            $product_id = SecurityManager::validateInteger($product_id);

            if (!$customer_id || !$product_id) {
                return false;
            }

            // Check if product exists in cart using c_id and p_id
            $sql = "SELECT p_id, qty as quantity FROM cart WHERE c_id = ? AND p_id = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                return false;
            }

            $stmt->bind_param('ii', $customer_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                return $result->fetch_assoc();
            }

            return false;

        } catch (Exception $e) {
            return false;
        }
    }
}
?>
