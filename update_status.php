<?php
include 'db_config.php';

$id = $_POST['id'];
$tier = $_POST['tier'];

$table = "qrcodegenerate";
if ($tier === 'vip') $table = "qrcodevip";
if ($tier === 'vvip') $table = "qrcodevvip";

$sql = "UPDATE $table SET status='verified' WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "success";
} else {
    echo "error";
}
?>