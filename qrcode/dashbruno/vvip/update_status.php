<?php
include 'db.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "UPDATE qrcodevvip SET verified = 'Verified' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }
}
?>