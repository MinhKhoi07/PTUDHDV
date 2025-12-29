<?php
require_once __DIR__ . '/../config.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : null;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 12;

$offset = ($page - 1) * $limit;

if (strlen($query) < 2) {
    ApiResponse::error('Search query must be at least 2 characters', 400);
}

// Build WHERE clause
$where = ["(p.product_name LIKE ? OR p.description LIKE ?)"];
$params = ["%$query%", "%$query%"];
$types = 'ss';

if ($category_id) {
    $where[] = 'p.category_id = ?';
    $params[] = $category_id;
    $types .= 'i';
}

if ($min_price !== null) {
    $where[] = 'p.price >= ?';
    $params[] = $min_price;
    $types .= 'i';
}

if ($max_price !== null) {
    $where[] = 'p.price <= ?';
    $params[] = $max_price;
    $types .= 'i';
}

$where_clause = implode(' AND ', $where);

// Count total
$count_sql = "SELECT COUNT(*) as total FROM products p WHERE $where_clause";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];

// Get results
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE $where_clause 
        ORDER BY p.product_id DESC 
        LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = [
        'product_id' => $row['product_id'],
        'product_name' => htmlspecialchars($row['product_name']),
        'price' => (float)$row['price'],
        'stock' => (int)$row['stock_quantity'],
        'image' => htmlspecialchars($row['image_url']),
        'category' => htmlspecialchars($row['category_name'] ?? '')
    ];
}

ApiResponse::paginated($products, $total, $page, $limit, 'Search results');
?>
