<?php
// controllers/order_controller.php
require_once __DIR__ . '/../classes/order_class.php';

class OrderController {
    private $order;

    public function __construct() {
        $this->order = new Order();
    }

    /**
     * Create a new order
     * @param array $params - Contains customer_id, order_reference, total_amount, status
     * @return array - Response
     */
    public function create_order_ctr($params) {
        return $this->order->createOrder($params);
    }
    
    /**
     * Add order details
     * @param array $params - Contains order_id, product_id, quantity, price
     * @return array - Response
     */
    public function add_order_details_ctr($params) {
        return $this->order->addOrderDetails($params);
    }
    
    /**
     * Record payment
     * @param array $params - Contains order_id, payment_method, payment_amount, payment_status
     * @return array - Response
     */
    public function record_payment_ctr($params) {
        return $this->order->recordPayment($params);
    }
    
    /**
     * Get past orders for a user
     * @param int $customer_id - Customer ID
     * @return array - Response
     */
    public function get_past_orders_ctr($customer_id) {
        return $this->order->getPastOrders($customer_id);
    }
}
?>
