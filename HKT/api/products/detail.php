<?php
require_once __DIR__ . '/../config.php';

$product_id = isset($_GET['id']) ? Validator::required($_GET['id'], 'Product ID') : null;

$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.product_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    ApiResponse::error('Product not found', 404);
}

$product = $result->fetch_assoc();

$data = [
    'product_id' => $product['product_id'],
    'product_name' => htmlspecialchars($product['product_name']),
    'price' => (float)$product['price'],
    'stock' => (int)$product['stock_quantity'],
    'sold' => (int)$product['sold_quantity'],
    'image' => htmlspecialchars($product['image_url']),
    'category_id' => (int)$product['category_id'],
    'category_name' => htmlspecialchars($product['category_name'] ?? ''),
    'description' => htmlspecialchars($product['description'] ?? ''),
    'in_stock' => (int)$product['stock_quantity'] > 0
];

ApiResponse::success($data, 'Product details retrieved successfully');
?>
