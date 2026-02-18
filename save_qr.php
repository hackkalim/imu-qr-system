<?php
include 'db_config.php';

$tier = $_POST['tier'];
$code = $_POST['qrcode'];
$price = $_POST['price'];

// Logic to match your specific table and column names
if ($tier === 'simple') {
    $table = "qrcodegenerate";
    $qrColumn = "qrcodegenerate"; // Column name for simple
} elseif ($tier === 'vip') {
    $table = "qrcodevip";
    $qrColumn = "qrcodevip"; // Column name for vip
} elseif ($tier === 'vvip') {
    $table = "qrcodevvip";
    $qrColumn = "qrcodevvip"; // Column name for vvip
}

// SQL using your requested column 'verified' with 'Pending' status and 'price' column
$sql = "INSERT INTO $table ($qrColumn, verified, price) VALUES (?, 'Pending', ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $code, $price);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error: " . $conn->error;
}
?>