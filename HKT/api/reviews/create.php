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
$order_id = $data['order_id'] ?? null;
$rating = Validator::required($data['rating'] ?? null, 'Rating');
$content = Validator::required($data['content'] ?? null, 'Review content');

// Validate rating
$rating = (int)$rating;
if ($rating < 1 || $rating > 5) {
    ApiResponse::error('Rating must be between 1 and 5', 400);
}

// Validate content length
$content = trim($content);
if (strlen($content) < 10) {
    ApiResponse::error('Review content must be at least 10 characters', 400);
}

if (strlen($content) > 1000) {
    ApiResponse::error('Review content must not exceed 1000 characters', 400);
}

$user_id = $_SESSION['user_id'];

// Check product exists
$check_sql = "SELECT product_id FROM products WHERE product_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $product_id);
$check_stmt->execute();

if ($check_stmt->get_result()->num_rows === 0) {
    ApiResponse::error('Product not found', 404);
}

// Check if user already reviewed this product
$review_check = "SELECT review_id FROM reviews WHERE user_id = ? AND product_id = ?";
$review_stmt = $conn->prepare($review_check);
$review_stmt->bind_param("is", $user_id, $product_id);
$review_stmt->execute();

if ($review_stmt->get_result()->num_rows > 0) {
    ApiResponse::error('You already reviewed this product', 400);
}

// Create review
$insert_sql = "INSERT INTO reviews (user_id, product_id, order_id, rating, content, review_date) 
               VALUES (?, ?, ?, ?, ?, NOW())";

$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("isiss", $user_id, $product_id, $order_id, $rating, $content);

if (!$insert_stmt->execute()) {
    ApiResponse::error('Failed to create review', 500);
}

$review_id = $conn->insert_id;

ApiResponse::success([
    'review_id' => $review_id,
    'product_id' => $product_id,
    'rating' => $rating,
    'content' => htmlspecialchars($content)
], 'Review created successfully', 201);
?>
