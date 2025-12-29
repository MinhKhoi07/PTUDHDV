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

$user_id = $_SESSION['user_id'];
$full_name = $data['full_name'] ?? null;
$phone = $data['phone'] ?? null;
$address = $data['address'] ?? null;

// Validate
if ($full_name) {
    $full_name = Validator::required($full_name, 'Full name');
}

// Update user
$update_sql = "UPDATE users SET ";
$updates = [];
$params = [];
$types = '';

if ($full_name) {
    $updates[] = "full_name = ?";
    $params[] = $full_name;
    $types .= 's';
}

if ($phone) {
    $updates[] = "phone = ?";
    $params[] = $phone;
    $types .= 's';
}

if ($address) {
    $updates[] = "address = ?";
    $params[] = $address;
    $types .= 's';
}

if (empty($updates)) {
    ApiResponse::error('No fields to update', 400);
}

$update_sql .= implode(', ', $updates) . " WHERE user_id = ?";
$params[] = $user_id;
$types .= 'i';

$stmt = $conn->prepare($update_sql);
$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    ApiResponse::error('Failed to update profile', 500);
}

ApiResponse::success([], 'Profile updated successfully');
?>
