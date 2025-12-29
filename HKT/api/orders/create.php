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

// Validate required fields
$full_name = Validator::required($data['full_name'] ?? null, 'Full name');
$email = Validator::email($data['email'] ?? null);
$phone = Validator::required($data['phone'] ?? null, 'Phone');
$address = Validator::required($data['address'] ?? null, 'Address');
$payment_method = $data['payment_method'] ?? 'cod';

$user_id = $_SESSION['user_id'];

// Get cart items
$cart_sql = "SELECT c.product_id, c.quantity, p.price, p.stock_quantity 
             FROM cart c 
             JOIN products p ON c.product_id = p.product_id 
             WHERE c.user_id = ?";

$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

if ($cart_result->num_rows === 0) {
    ApiResponse::error('Cart is empty', 400);
}

$total_amount = 0;
$items = [];

while ($item = $cart_result->fetch_assoc()) {
    // Check stock
    if ($item['stock_quantity'] < $item['quantity']) {
        ApiResponse::error("Not enough stock for product {$item['product_id']}", 400);
    }
    
    $subtotal = (float)$item['price'] * $item['quantity'];
    $total_amount += $subtotal;
    $items[] = $item;
}

try {
    $conn->begin_transaction();
    
    // Create order
    $order_status = $payment_method === 'bank_transfer' ? 'Chờ thanh toán' : 'Chờ xác nhận';
    
    $order_sql = "INSERT INTO orders 
                  (user_id, full_name, email, phone, address, total_amount, payment_method, order_status, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("issssds", $user_id, $full_name, $email, $phone, $address, $total_amount, $payment_method, $order_status);
    $order_stmt->execute();
    
    $order_id = $conn->insert_id;
    
    // Add order details
    $detail_sql = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $detail_stmt = $conn->prepare($detail_sql);
    
    foreach ($items as $item) {
        $detail_stmt->bind_param("isid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $detail_stmt->execute();
    }
    
    // Clear cart
    $clear_sql = "DELETE FROM cart WHERE user_id = ?";
    $clear_stmt = $conn->prepare($clear_sql);
    $clear_stmt->bind_param("i", $user_id);
    $clear_stmt->execute();
    
    $conn->commit();
    
    ApiResponse::success([
        'order_id' => $order_id,
        'total' => $total_amount,
        'payment_method' => $payment_method,
        'status' => $order_status
    ], 'Order created successfully', 201);
    
} catch (Exception $e) {
    $conn->rollback();
    ApiResponse::error('Order creation failed: ' . $e->getMessage(), 500);
}
?>
