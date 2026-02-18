<?php
session_start();
include 'db_config.php';

$user = $_POST['username'];
$pass = $_POST['password'];

// 1. Fetch only the user by their username
$stmt = $conn->prepare("SELECT password FROM admin_users WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hashed_password = $row['password'];

    // 2. Verify the plain-text password against the hash from DB
    if (password_verify($pass, $hashed_password)) {
        $_SESSION['admin_logged_in'] = true;
        // Update last activity to sync with your index.php timeout
        $_SESSION['last_activity'] = time(); 
        
        header("Location: dashbruno/index.php");
        exit();
    } else {
        // Password mismatch
        header("Location: login.php?error=1");
        exit();
    }
} else {
    // User not found
    header("Location: login.php?error=1");
    exit();
}
?>