<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

require_once 'db_config.php';

// TEMPORARILY DISABLED AUTH FOR TESTING
// Just get the data directly

$sql = "SELECT id, qrcodevip as qr_code, verified, price 
        FROM qrcodevip
        ORDER BY id DESC";
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