<?php
require_once __DIR__ . '/../config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    ApiResponse::error('Unauthorized', 401);
}

$user_id = $_SESSION['user_id'];

// Total orders
$orders_sql = "SELECT COUNT(*) as count FROM orders WHERE user_id = ?";
$orders_stmt = $conn->prepare($orders_sql);
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$total_orders = $orders_stmt->get_result()->fetch_assoc()['count'];

// Total spent
$spent_sql = "SELECT SUM(total_amount) as total FROM orders WHERE user_id = ? AND order_status != 'Đã hủy'";
$spent_stmt = $conn->prepare($spent_sql);
$spent_stmt->bind_param("i", $user_id);
$spent_stmt->execute();
$total_spent = $spent_stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Wishlist count
$wishlist_sql = "SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?";
$wishlist_stmt = $conn->prepare($wishlist_sql);
$wishlist_stmt->bind_param("i", $user_id);
$wishlist_stmt->execute();
$wishlist_count = $wishlist_stmt->get_result()->fetch_assoc()['count'];

// Reviews count
$reviews_sql = "SELECT COUNT(*) as count FROM reviews WHERE user_id = ?";
$reviews_stmt = $conn->prepare($reviews_sql);
$reviews_stmt->bind_param("i", $user_id);
$reviews_stmt->execute();
$reviews_count = $reviews_stmt->get_result()->fetch_assoc()['count'];

ApiResponse::success([
    'total_orders' => (int)$total_orders,
    'total_spent' => (float)$total_spent,
    'wishlist_count' => (int)$wishlist_count,
    'reviews_count' => (int)$reviews_count
], 'Dashboard stats retrieved successfully');
?>
