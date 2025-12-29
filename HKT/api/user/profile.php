<?php
require_once __DIR__ . '/../config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    ApiResponse::error('Unauthorized', 401);
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT user_id, username, email, full_name, phone, address, created_at 
        FROM users 
        WHERE user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    ApiResponse::error('User not found', 404);
}

ApiResponse::success([
    'user_id' => (int)$user['user_id'],
    'username' => htmlspecialchars($user['username']),
    'email' => htmlspecialchars($user['email']),
    'full_name' => htmlspecialchars($user['full_name']),
    'phone' => htmlspecialchars($user['phone'] ?? ''),
    'address' => htmlspecialchars($user['address'] ?? ''),
    'created_at' => $user['created_at']
], 'Profile retrieved successfully');
?>
