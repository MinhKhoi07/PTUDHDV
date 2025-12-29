<?php
require_once __DIR__ . '/../config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Invalid request method', 405);
}

$data = json_decode(file_get_contents('php://input'), true);

$username = Validator::required($data['username'] ?? null, 'Username');
$email = Validator::email($data['email'] ?? null);
$password = Validator::required($data['password'] ?? null, 'Password');
$password = Validator::min($password, 6, 'Password');
$full_name = Validator::required($data['full_name'] ?? null, 'Full name');

// Check if username exists
$check_sql = "SELECT user_id FROM users WHERE username = ? OR email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ss", $username, $email);
$check_stmt->execute();

if ($check_stmt->get_result()->num_rows > 0) {
    ApiResponse::error('Username or email already exists', 400);
}

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$insert_sql = "INSERT INTO users (username, password, email, full_name) VALUES (?, ?, ?, ?)";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("ssss", $username, $hashed_password, $email, $full_name);

if (!$insert_stmt->execute()) {
    ApiResponse::error('Registration failed', 500);
}

$user_id = $conn->insert_id;
$_SESSION['user_id'] = $user_id;
$_SESSION['username'] = $username;
$_SESSION['email'] = $email;

ApiResponse::success([
    'user_id' => $user_id,
    'username' => htmlspecialchars($username),
    'email' => htmlspecialchars($email),
    'full_name' => htmlspecialchars($full_name)
], 'Registration successful', 201);
?>
