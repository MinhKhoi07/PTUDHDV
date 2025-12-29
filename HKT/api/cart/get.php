<?php
require_once __DIR__ . '/../config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    ApiResponse::error('Unauthorized', 401);
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT c.cart_id, c.quantity, p.product_id, p.product_name, p.price, p.image_url, p.stock_quantity
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $subtotal = (float)$row['price'] * $row['quantity'];
    $total += $subtotal;
    
    $items[] = [
        'cart_id' => (int)$row['cart_id'],
        'product_id' => $row['product_id'],
        'product_name' => htmlspecialchars($row['product_name']),
        'price' => (float)$row['price'],
        'quantity' => (int)$row['quantity'],
        'subtotal' => $subtotal,
        'image' => htmlspecialchars($row['image_url']),
        'stock' => (int)$row['stock_quantity']
    ];
}

ApiResponse::success([
    'items' => $items,
    'total' => $total,
    'count' => count($items)
], 'Cart retrieved successfully');
?>
