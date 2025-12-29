<?php
require_once __DIR__ . '/../config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Invalid request method', 405);
}

$data = json_decode(file_get_contents('php://input'), true);

$username = Validator::required($data['username'] ?? null, 'Username');
$password = Validator::required($data['password'] ?? null, 'Password');

$sql = "SELECT user_id, username, password, full_name, email FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    ApiResponse::error('Invalid credentials', 401);
}

$_SESSION['user_id'] = $user['user_id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];

ApiResponse::success([
    'user_id' => $user['user_id'],
    'username' => htmlspecialchars($user['username']),
    'email' => htmlspecialchars($user['email']),
    'full_name' => htmlspecialchars($user['full_name'])
], 'Login successful');
?>
