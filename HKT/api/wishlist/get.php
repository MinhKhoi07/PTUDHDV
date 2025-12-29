<?php
require_once __DIR__ . '/../config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    ApiResponse::error('Unauthorized', 401);
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT w.wishlist_id, p.* 
        FROM wishlist w 
        JOIN products p ON w.product_id = p.product_id 
        WHERE w.user_id = ? 
        ORDER BY w.added_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = [
        'wishlist_id' => (int)$row['wishlist_id'],
        'product_id' => $row['product_id'],
        'product_name' => htmlspecialchars($row['product_name']),
        'price' => (float)$row['price'],
        'image' => htmlspecialchars($row['image_url']),
        'stock' => (int)$row['stock_quantity'],
        'in_stock' => (int)$row['stock_quantity'] > 0
    ];
}

ApiResponse::success([
    'items' => $items,
    'count' => count($items)
], 'Wishlist retrieved successfully');
?>
