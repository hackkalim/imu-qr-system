<?php
session_start();
include 'db_config.php';

// Check if the user is logged in before allowing a password change
if (!isset($_SESSION['admin_logged_in'])) {
    exit("unauthorized");
}

if (isset($_POST['new_user']) && isset($_POST['new_pass'])) {
    $new_user = $_POST['new_user'];
    $new_pass = $_POST['new_pass'];

    // 1. Hash the password using the current standard algorithm (BCRYPT)
    // This turns a plain-text password into a secure string that cannot be reversed.
    $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);

    // 2. Use a Prepared Statement for security
    // This prevents hackers from manipulating your database via the input fields.
    $stmt = $conn->prepare("UPDATE admin_users SET username = ?, password = ? WHERE id = 1");
    $stmt->bind_param("ss", $new_user, $hashed_pass);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
}
?>