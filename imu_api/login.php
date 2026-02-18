<?php
// Enable CORS for React Native
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db_config.php';

// Rate limiting implementation
$ip_address = $_SERVER['REMOTE_ADDR'];
$rate_limit_file = "rate_limit_{$ip_address}.txt";
$current_time = time();
$max_attempts = 5;
$time_window = 300; // 5 minutes

// Check rate limit
if (file_exists($rate_limit_file)) {
    $attempts = json_decode(file_get_contents($rate_limit_file), true);
    // Clean old attempts
    $attempts = array_filter($attempts, function($time) use ($current_time, $time_window) {
        return ($current_time - $time) < $time_window;
    });
    
    if (count($attempts) >= $max_attempts) {
        echo json_encode([
            'success' => false,
            'message' => 'Too many login attempts. Please try again later.',
            'error_code' => 'RATE_LIMIT'
        ]);
        exit();
    }
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['username']) || !isset($input['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Username and password are required'
    ]);
    exit();
}

$username = $conn->real_escape_string(trim($input['username']));
$password = trim($input['password']);

// Query the simple_users table - only get the first user (id = 1)
$sql = "SELECT id, username, password, created_at FROM simple_users WHERE id = 1 AND username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Record this attempt
$attempts[] = $current_time;
file_put_contents($rate_limit_file, json_encode($attempts));

if ($row = $result->fetch_assoc()) {
    // Verify password
    if (password_verify($password, $row['password'])) {
        // Generate secure session token
        $session_token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        
        // Store session in database (create sessions table if not exists)
        $create_table = "CREATE TABLE IF NOT EXISTS user_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            session_token VARCHAR(255) NOT NULL UNIQUE,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP,
            is_active BOOLEAN DEFAULT TRUE,
            FOREIGN KEY (user_id) REFERENCES simple_users(id)
        )";
        $conn->query($create_table);
        
        // Store session
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $insert_sql = "INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) 
                       VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("issss", $row['id'], $session_token, $ip_address, $user_agent, $expires_at);
        $insert_stmt->execute();
        
        // Clear rate limit on successful login
        if (file_exists($rate_limit_file)) {
            unlink($rate_limit_file);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user_id' => $row['id'],
                'username' => $row['username'],
                'session_token' => $session_token,
                'expires_at' => $expires_at
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid credentials',
            'error_code' => 'INVALID_PASSWORD'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'User not found',
        'error_code' => 'USER_NOT_FOUND'
    ]);
}

$stmt->close();
$conn->close();
?>