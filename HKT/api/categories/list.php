<?php
require_once __DIR__ . '/../config.php';

$sql = "SELECT c.*, COUNT(p.product_id) as product_count 
        FROM categories c 
        LEFT JOIN products p ON c.category_id = p.category_id 
        GROUP BY c.category_id 
        ORDER BY c.category_name ASC";

$result = $conn->query($sql);

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = [
        'category_id' => (int)$row['category_id'],
        'category_name' => htmlspecialchars($row['category_name']),
        'description' => htmlspecialchars($row['description'] ?? ''),
        'product_count' => (int)$row['product_count']
    ];
}

ApiResponse::success($categories, 'Categories retrieved successfully');
?>
