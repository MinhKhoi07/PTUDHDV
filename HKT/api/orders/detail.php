<?php
require_once __DIR__ . '/../config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    ApiResponse::error('Unauthorized', 401);
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$order_id) {
    ApiResponse::error('Order ID is required', 400);
}

// Get order
$sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    ApiResponse::error('Order not found', 404);
}

// Get order details
$detail_sql = "SELECT od.*, p.product_name, p.image_url 
               FROM order_details od 
               JOIN products p ON od.product_id = p.product_id 
               WHERE od.order_id = ?";

$detail_stmt = $conn->prepare($detail_sql);
$detail_stmt->bind_param("i", $order_id);
$detail_stmt->execute();
$details_result = $detail_stmt->get_result();

$items = [];
while ($item = $details_result->fetch_assoc()) {
    $items[] = [
        'product_id' => htmlspecialchars($item['product_id']),
        'product_name' => htmlspecialchars($item['product_name']),
        'quantity' => (int)$item['quantity'],
        'price' => (float)$item['price'],
        'subtotal' => (float)$item['quantity'] * (float)$item['price'],
        'image' => htmlspecialchars($item['image_url'])
    ];
}

ApiResponse::success([
    'order_id' => (int)$order['order_id'],
    'full_name' => htmlspecialchars($order['full_name']),
    'email' => htmlspecialchars($order['email']),
    'phone' => htmlspecialchars($order['phone']),
    'address' => htmlspecialchars($order['address']),
    'total_amount' => (float)$order['total_amount'],
    'status' => htmlspecialchars($order['order_status']),
    'payment_method' => htmlspecialchars($order['payment_method']),
    'notes' => htmlspecialchars($order['notes'] ?? ''),
    'items' => $items,
    'created_at' => $order['created_at']
], 'Order details retrieved successfully');
?>
