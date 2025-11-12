<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../controllers/order_controller.php';
require_once __DIR__ . '/../classes/order_class.php';

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Please login to checkout'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$customer_id = get_user_id();

try {
    // Get cart items
    $cartController = new CartController();
    $cartResult = $cartController->get_user_cart_ctr($customer_id);

    if (!$cartResult['success'] || empty($cartResult['data'])) {
        echo json_encode(['success' => false, 'message' => 'Your cart is empty'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    $cart_items = $cartResult['data'];
    $total_amount = $cartResult['total'] ?? 0;

    // Generate unique order reference
    $order_reference = Order::generateOrderReference();

    // Create order
    $orderController = new OrderController();
    $orderResult = $orderController->create_order_ctr([
        'customer_id' => $customer_id,
        'order_reference' => $order_reference,
        'total_amount' => $total_amount,
        'status' => 'pending'
    ]);

    if (!$orderResult['success']) {
        echo json_encode(['success' => false, 'message' => 'Failed to create order: ' . $orderResult['message']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    $order_id = $orderResult['order_id'];

    // Add order details for each cart item
    $orderDetailsSuccess = true;
    $orderDetailsErrors = [];

    foreach ($cart_items as $item) {
        $detailResult = $orderController->add_order_details_ctr([
            'order_id' => $order_id,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'price' => $item['product_price']
        ]);

        if (!$detailResult['success']) {
            $orderDetailsSuccess = false;
            $orderDetailsErrors[] = $detailResult['message'];
        }
    }

    if (!$orderDetailsSuccess) {
        echo json_encode(['success' => false, 'message' => 'Failed to add order details: ' . implode(', ', $orderDetailsErrors)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Record payment
    $paymentResult = $orderController->record_payment_ctr([
        'order_id' => $order_id,
        'payment_method' => 'simulated',
        'payment_amount' => $total_amount,
        'payment_status' => 'completed'
    ]);

    if (!$paymentResult['success']) {
        echo json_encode(['success' => false, 'message' => 'Failed to record payment: ' . $paymentResult['message']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Empty cart
    $emptyCartResult = $cartController->empty_cart_ctr($customer_id);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $order_id,
        'order_reference' => $order_reference,
        'total_amount' => $total_amount
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
?>
