<?php
require_once __DIR__ . '/../config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    ApiResponse::error('Unauthorized', 401);
}

$data = json_decode(file_get_contents('php://input'), true);

$product_id = Validator::required($data['product_id'] ?? null, 'Product ID');
$quantity = Validator::required($data['quantity'] ?? 1, 'Quantity');
$quantity = Validator::number($quantity, 'Quantity');
$quantity = max(1, (int)$quantity);

$user_id = $_SESSION['user_id'];

// Check product exists
$check_sql = "SELECT product_id, stock_quantity, price, product_name FROM products WHERE product_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $product_id);
$check_stmt->execute();
$product = $check_stmt->get_result()->fetch_assoc();

if (!$product) {
    ApiResponse::error('Product not found', 404);
}

if ($product['stock_quantity'] < $quantity) {
    ApiResponse::error('Not enough stock available', 400);
}

// Check if already in cart
$check_cart = "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
$cart_stmt = $conn->prepare($check_cart);
$cart_stmt->bind_param("is", $user_id, $product_id);
$cart_stmt->execute();
$cart_item = $cart_stmt->get_result()->fetch_assoc();

if ($cart_item) {
    // Update quantity
    $new_quantity = $cart_item['quantity'] + $quantity;
    if ($product['stock_quantity'] < $new_quantity) {
        ApiResponse::error('Not enough stock for requested quantity', 400);
    }
    
    $update_sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $new_quantity, $cart_item['cart_id']);
    $update_stmt->execute();
} else {
    // Add new item
    $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("isi", $user_id, $product_id, $quantity);
    $insert_stmt->execute();
}

ApiResponse::success([
    'product_name' => htmlspecialchars($product['product_name']),
    'quantity' => $quantity
], 'Added to cart successfully');
?>
