<?php
session_start();
include 'db_config.php';

if (isset($_POST['new_user']) && isset($_POST['new_pass'])) {
    $new_user = $_POST['new_user'];
    $new_pass = $_POST['new_pass'];

    // Update the first admin in the table
    $sql = "UPDATE vip_users SET username='$new_user', password='$new_pass' WHERE id=1";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }
}
?>