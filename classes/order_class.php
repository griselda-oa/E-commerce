<?php
// classes/order_class.php
require_once __DIR__ . '/../settings/db_class.php';
require_once __DIR__ . '/../settings/security.php';

class Order extends db_connection
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create a new order in the orders table
     * @param array $args - Contains customer_id, order_reference, total_amount, status
     * @return array - Success/failure response with order_id
     */
    public function createOrder($args)
    {
        try {
            $customer_id = SecurityManager::validateInteger($args['customer_id'] ?? 0);
            $order_reference = SecurityManager::sanitizeString($args['order_reference'] ?? '');
            $total_amount = floatval($args['total_amount'] ?? 0);
            $status = SecurityManager::sanitizeString($args['status'] ?? 'pending');

            if (!$customer_id) {
                return array('success' => false, 'message' => 'Valid customer ID is required');
            }

            if (empty($order_reference)) {
                return array('success' => false, 'message' => 'Order reference is required');
            }

            // Using actual column names: customer_id, invoice_no, order_status
            // Note: invoice_no replaces order_reference, and there's no total_amount column
            $sql = "INSERT INTO orders (customer_id, invoice_no, order_status) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }

            $stmt->bind_param('iss', $customer_id, $order_reference, $status);

            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Order created successfully', 'order_id' => $this->db->insert_id);
            } else {
                return array('success' => false, 'message' => 'Failed to create order: ' . $stmt->error);
            }

        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Add order details (product ID, quantity, price) to orderdetails table
     * @param array $args - Contains order_id, product_id, quantity, price
     * @return array - Success/failure response
     */
    public function addOrderDetails($args)
    {
        try {
            $order_id = SecurityManager::validateInteger($args['order_id'] ?? 0);
            $product_id = SecurityManager::validateInteger($args['product_id'] ?? 0);
            $quantity = SecurityManager::validateInteger($args['quantity'] ?? 0);

            if (!$order_id) {
                return array('success' => false, 'message' => 'Valid order ID is required');
            }

            if (!$product_id) {
                return array('success' => false, 'message' => 'Valid product ID is required');
            }

            if ($quantity <= 0) {
                return array('success' => false, 'message' => 'Quantity must be greater than 0');
            }

            // Using actual column names: order_id, product_id, qty (no price column in orderdetails table)
            $sql = "INSERT INTO orderdetails (order_id, product_id, qty) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }

            $stmt->bind_param('iii', $order_id, $product_id, $quantity);

            if ($stmt->execute()) {
                // Note: orderdetails table has order_id, product_id, qty (no separate order_detail_id column visible)
                return array('success' => true, 'message' => 'Order detail added successfully');
            } else {
                return array('success' => false, 'message' => 'Failed to add order detail: ' . $stmt->error);
            }

        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Record simulated payment entry in payments table
     * @param array $args - Contains order_id, payment_method, payment_amount, payment_status
     * @return array - Success/failure response
     */
    public function recordPayment($args)
    {
        try {
            $order_id = SecurityManager::validateInteger($args['order_id'] ?? 0);
            $payment_method = SecurityManager::sanitizeString($args['payment_method'] ?? 'simulated');
            $payment_amount = floatval($args['payment_amount'] ?? 0);
            $payment_status = SecurityManager::sanitizeString($args['payment_status'] ?? 'completed');

            if (!$order_id) {
                return array('success' => false, 'message' => 'Valid order ID is required');
            }

            if ($payment_amount <= 0) {
                return array('success' => false, 'message' => 'Payment amount must be greater than 0');
            }

            $sql = "INSERT INTO payments (order_id, payment_method, payment_amount, payment_status) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                return array('success' => false, 'message' => 'Database error: ' . $this->db->error);
            }

            $stmt->bind_param('isds', $order_id, $payment_method, $payment_amount, $payment_status);

            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Payment recorded successfully', 'payment_id' => $this->db->insert_id);
            } else {
                return array('success' => false, 'message' => 'Failed to record payment: ' . $stmt->error);
            }

        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Get past orders for a user
     * @param int $customer_id - Customer ID
     * @return array - Response with orders
     */
    public function getPastOrders($customer_id)
    {
        try {
            $customer_id = SecurityManager::validateInteger($customer_id);

            if (!$customer_id) {
                return array('success' => false, 'message' => 'Valid customer ID is required', 'data' => []);
            }

            $sql = "SELECT 
                        o.order_id,
                        o.invoice_no as order_reference,
                        o.order_date,
                        o.order_status as status,
                        p.payment_method,
                        p.payment_status,
                        p.payment_date
                    FROM orders o
                    LEFT JOIN payments p ON o.order_id = p.order_id
                    WHERE o.customer_id = ?
                    ORDER BY o.order_date DESC";

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

            $orders = array();
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }

            return array(
                'success' => true,
                'message' => 'Orders retrieved successfully',
                'data' => $orders
            );

        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error: ' . $e->getMessage(), 'data' => []);
        }
    }

    /**
     * Generate a unique numeric invoice number
     * Format: YYYYMMDD + random 6 digits (e.g., 20251112012345)
     * @return int - Unique numeric invoice number
     */
    public static function generateOrderReference()
    {
        // Generate numeric invoice: YYYYMMDD + 6 random digits
        $datePart = date('Ymd');
        $randomPart = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        return intval($datePart . $randomPart);
    }
}
?>
