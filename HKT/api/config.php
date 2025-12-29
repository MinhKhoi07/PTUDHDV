<?php
// Disable direct access
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    http_response_code(403);
    die('Direct access not allowed');
}

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
require_once __DIR__ . '/../config/connect.php';

// API Response helper class
class ApiResponse {
    public static function success($data = null, $message = 'Success', $code = 200) {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
        exit();
    }

    public static function error($message = 'Error', $code = 400, $data = null) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'message' => $message,
            'data' => $data
        ]);
        exit();
    }

    public static function paginated($items, $total, $page, $limit, $message = 'Success') {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $items,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit)
            ]
        ]);
        exit();
    }
}

// Validation helper
class Validator {
    public static function required($value, $field) {
        if (empty($value)) {
            ApiResponse::error("$field is required", 400);
        }
        return $value;
    }

    public static function email($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            ApiResponse::error('Invalid email format', 400);
        }
        return $email;
    }

    public static function number($value, $field) {
        if (!is_numeric($value)) {
            ApiResponse::error("$field must be a number", 400);
        }
        return $value;
    }

    public static function min($value, $min, $field) {
        if (strlen($value) < $min) {
            ApiResponse::error("$field must be at least $min characters", 400);
        }
        return $value;
    }
}
?>
