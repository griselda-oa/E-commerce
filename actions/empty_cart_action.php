<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Please login'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$customer_id = get_user_id();

$cartController = new CartController();
$result = $cartController->empty_cart_ctr($customer_id);

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
