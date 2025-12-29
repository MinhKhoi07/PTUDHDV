<?php
require_once __DIR__ . '/../config.php';

$product_id = isset($_GET['product_id']) ? Validator::required($_GET['product_id'], 'Product ID') : null;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? min(50, max(1, (int)$_GET['limit'])) : 10;

$offset = ($page - 1) * $limit;

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM reviews WHERE product_id = ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("s", $product_id);
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];

// Get reviews
$sql = "SELECT r.*, u.username, u.full_name 
        FROM reviews r 
        JOIN users u ON r.user_id = u.user_id 
        WHERE r.product_id = ? 
        ORDER BY r.review_date DESC 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $product_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = [
        'review_id' => (int)$row['review_id'],
        'user' => htmlspecialchars($row['full_name'] ?? $row['username']),
        'rating' => (int)$row['rating'],
        'content' => htmlspecialchars($row['content']),
        'date' => date('d/m/Y H:i', strtotime($row['review_date'])),
        'images' => json_decode($row['images'] ?? '[]', true)
    ];
}

// Calculate average rating
$avg_sql = "SELECT AVG(rating) as average FROM reviews WHERE product_id = ?";
$avg_stmt = $conn->prepare($avg_sql);
$avg_stmt->bind_param("s", $product_id);
$avg_stmt->execute();
$avg_rating = $avg_stmt->get_result()->fetch_assoc()['average'];

ApiResponse::paginated($reviews, $total, $page, $limit, 'Reviews retrieved successfully');
?>
