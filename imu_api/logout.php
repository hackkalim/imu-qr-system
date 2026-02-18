<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db_config.php';

$headers = getallheaders();
$session_token = $headers['Authorization'] ?? '';

if (!empty($session_token)) {
    $session_token = str_replace('Bearer ', '', $session_token);
    
    // Deactivate session
    $update_sql = "UPDATE user_sessions SET is_active = FALSE WHERE session_token = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("s", $session_token);
    $stmt->execute();
}

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
$conn->close();
?>