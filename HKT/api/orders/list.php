<?php
require_once __DIR__ . '/../config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    ApiResponse::error('Unauthorized', 401);
}

$user_id = $_SESSION['user_id'];
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(50, max(1, (int)$_GET['limit'])) : 10;
$status = isset($_GET['status']) ? $_GET['status'] : null;

$offset = ($page - 1) * $limit;

// Build query
$where = "o.user_id = ?";
$params = [$user_id];
$types = 'i';

if ($status) {
    $where .= " AND o.order_status = ?";
    $params[] = $status;
    $types .= 's';
}

// Count total
$count_sql = "SELECT COUNT(*) as total FROM orders o WHERE $where";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];

// Get orders
$sql = "SELECT o.order_id, o.full_name, o.total_amount, o.order_status, 
               o.payment_method, o.created_at, COUNT(od.order_detail_id) as item_count
        FROM orders o 
        LEFT JOIN order_details od ON o.order_id = od.order_id
        WHERE $where 
        GROUP BY o.order_id
        ORDER BY o.created_at DESC 
        LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = [
        'order_id' => (int)$row['order_id'],
        'full_name' => htmlspecialchars($row['full_name']),
        'total_amount' => (float)$row['total_amount'],
        'status' => htmlspecialchars($row['order_status']),
        'payment_method' => htmlspecialchars($row['payment_method']),
        'item_count' => (int)$row['item_count'],
        'created_at' => $row['created_at']
    ];
}

ApiResponse::paginated($orders, $total, $page, $limit, 'Orders retrieved successfully');
?>
