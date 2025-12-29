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

// Check product exists
$check_sql = "SELECT product_id FROM products WHERE product_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $product_id);
$check_stmt->execute();

if ($check_stmt->get_result()->num_rows === 0) {
    ApiResponse::error('Product not found', 404);
}

// Check if already in wishlist
$check_wishlist = "SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?";
$wishlist_stmt = $conn->prepare($check_wishlist);
$wishlist_stmt->bind_param("is", $user_id, $product_id);
$wishlist_stmt->execute();

if ($wishlist_stmt->get_result()->num_rows > 0) {
    ApiResponse::error('Product already in wishlist', 400);
}

// Add to wishlist
$insert_sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("is", $user_id, $product_id);

if (!$insert_stmt->execute()) {
    ApiResponse::error('Failed to add to wishlist', 500);
}

ApiResponse::success(['product_id' => $product_id], 'Added to wishlist successfully', 201);
?>
