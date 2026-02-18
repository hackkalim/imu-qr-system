<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "qrcodegenerate");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query to get records
$sql = "SELECT * FROM qrcodegenerate WHERE qrcodegenerate LIKE '%$search%' ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $qr = $row['qrcodegenerate'];
        $price = $row['price'];
        $status = $row['verified']; // This will be 'Pending' by default

        // Logic to determine what to show in the Action column
        $actionButton = "";
        $badgeClass = "";

        if ($status == 'Pending') {
            $badgeClass = 'pending';
            // THIS IS THE BUTTON YOU WERE MISSING
            $actionButton = "<button class='btn-check' onclick='checkout($id)'>Checkout</button>";
        } else {
            $badgeClass = 'verified';
            $actionButton = "<span style='color: #2d6a4f; font-weight: 800;'>Verified ✅</span>";
        }

        echo "<tr>
                <td>#$id</td>
                <td><strong>$qr</strong></td>
                <td style='color:green;'><strong>$price Fbu</strong></td>
                <td><span class='badge $badgeClass'>$status</span></td>
                <td>$actionButton</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4' style='text-align:center; padding: 20px;'>No QR codes found.</td></tr>";
}

$conn->close();
?>