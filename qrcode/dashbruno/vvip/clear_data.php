<?php
include 'db.php';

// This command empties the table and resets the ID counter to 1
$sql = "TRUNCATE TABLE qrcodevvip";

if ($conn->query($sql) === TRUE) {
    echo "success";
} else {
    echo "error";
}

$conn->close();
?>