<?php
include 'db_config.php';

// Empty all three tables
$q1 = $conn->query("TRUNCATE TABLE qrcodegenerate");
$q2 = $conn->query("TRUNCATE TABLE qrcodevip");
$q3 = $conn->query("TRUNCATE TABLE qrcodevvip");

if ($q1 && $q2 && $q3) {
    echo "success";
} else {
    echo "error";
}
?>