<?php
// controllers/cart_controller.php
require_once __DIR__ . '/../classes/cart_class.php';

class CartController {
    private $cart;

    public function __construct() {
        $this->cart = new Cart();
    }

    /**
     * Add a product to the cart
     * @param array $params - Contains customer_id, product_id, quantity
     * @return array - Response
     */
    public function add_to_cart_ctr($params) {
        return $this->cart->add($params);
    }
    
    /**
     * Update cart item quantity
     * @param int $cart_id - Cart item ID
     * @param int $qty - New quantity
     * @return array - Response
     */
    public function update_cart_item_ctr($cart_id, $qty) {
        return $this->cart->updateQuantity($cart_id, $qty);
    }
    
    /**
     * Remove item from cart
     * @param int $cart_id - Cart item ID
     * @return array - Response
     */
    public function remove_from_cart_ctr($cart_id) {
        return $this->cart->remove($cart_id);
    }
    
    /**
     * Get user's cart
     * @param int $customer_id - Customer ID
     * @return array - Response
     */
    public function get_user_cart_ctr($customer_id) {
        return $this->cart->getCart($customer_id);
    }
    
    /**
     * Empty user's cart
     * @param int $customer_id - Customer ID
     * @return array - Response
     */
    public function empty_cart_ctr($customer_id) {
        return $this->cart->emptyCart($customer_id);
    }
}
?>
