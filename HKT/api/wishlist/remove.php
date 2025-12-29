<?php
require_once __DIR__ . '/../config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    ApiResponse::error('Unauthorized', 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Invalid request method', 405);
}

$data = json_decode(file_get_contents('php://input'), true);

$product_id = Validator::required($data['product_id'] ?? null, 'Product ID');
$user_id = $_SESSION['user_id'];

// Delete from wishlist
$delete_sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("is", $user_id, $product_id);

if (!$delete_stmt->execute()) {
    ApiResponse::error('Failed to remove from wishlist', 500);
}

if ($delete_stmt->affected_rows === 0) {
    ApiResponse::error('Product not in wishlist', 404);
}

ApiResponse::success([], 'Removed from wishlist successfully');
?>
