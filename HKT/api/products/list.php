<?php
require_once __DIR__ . '/../config.php';

// Get query parameters
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 12;
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$offset = ($page - 1) * $limit;

// Build query
$where = ['1=1'];
$params = [];
$types = '';

if ($category_id) {
    $where[] = 'p.category_id = ?';
    $params[] = $category_id;
    $types .= 'i';
}

if ($search) {
    $where[] = '(p.product_name LIKE ? OR p.description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

$where_clause = implode(' AND ', $where);

// Order clause
$order_map = [
    'newest' => 'p.product_id DESC',
    'oldest' => 'p.product_id ASC',
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'popular' => 'p.sold_quantity DESC'
];
$order_clause = $order_map[$sort] ?? $order_map['newest'];

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM products p WHERE $where_clause";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];

// Get products
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE $where_clause 
        ORDER BY $order_clause 
        LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = [
        'product_id' => $row['product_id'],
        'product_name' => htmlspecialchars($row['product_name']),
        'price' => (float)$row['price'],
        'stock' => (int)$row['stock_quantity'],
        'sold' => (int)$row['sold_quantity'],
        'image' => htmlspecialchars($row['image_url']),
        'category' => htmlspecialchars($row['category_name'] ?? ''),
        'description' => htmlspecialchars(substr($row['description'] ?? '', 0, 100))
    ];
}

ApiResponse::paginated($products, $total, $page, $limit, 'Products retrieved successfully');
?>
