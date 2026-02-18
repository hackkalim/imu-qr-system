<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

require_once 'db_config.php';

// TEMPORARILY DISABLED AUTH FOR TESTING
// Just get the data directly

$sql = "SELECT id, qrcodegenerate as qr_code, verified, price 
        FROM qrcodegenerate 
        ORDER BY id DESC 
        LIMIT 5000";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['id'],
            'qr_code' => $row['qr_code'],
            'verified' => $row['verified'],
            'price' => (float)$row['price']
        ];
    }
}

echo json_encode([
    'success' => true,
    'data' => $data
]);

$conn->close();
?>