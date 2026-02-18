<?php
include 'db_config.php';

// Get tier and mode from AJAX request
$tier = $_GET['tier'] ?? 'simple';
$mode = $_GET['mode'] ?? 'full';

// Mapping tables and their specific QR column names
if ($tier === 'vip') {
    $table = "qrcodevip";
    $qrCol = "qrcodevip";    // Correct column for VIP table
} elseif ($tier === 'vvip') {
    $table = "qrcodevvip";
    $qrCol = "qrcodevvip";   // Correct column for VVIP table
} else {
    $table = "qrcodegenerate";
    $qrCol = "qrcodegenerate"; // Correct column for Simple table
}

// Perform the query
$sql = "SELECT * FROM $table ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        
        // 1. Handle Status Logic
        $statusLabel = $row['verified'] ?? 'Pending'; 
        $statusClass = (strtolower($statusLabel) == 'verified') ? 'verified' : 'pending';
        
        // 2. Handle Price Logic
        $priceDisplay = isset($row['price']) ? $row['price'] . " Fbu" : "0 Fbu";

        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        
        // THE FIX: Use $qrCol variable. 
        // We use isset() to prevent the "Undefined array key" warning if the DB column is missing.
        if (isset($row[$qrCol])) {
            echo "<td>" . $row[$qrCol] . "</td>";
        } else {
            echo "<td><i style='color:red'>Column $qrCol missing</i></td>";
        }
        
        echo "<td>" . $priceDisplay . "</td>";
        echo "<td><span class='badge $statusClass'>" . $statusLabel . "</span></td>";
        
        // 3. Action Button (Only for specific panels)
        if ($mode === 'full') {
            echo "<td>";
            if (strtolower($statusLabel) == 'pending') {
                echo "<button class='btn-check' onclick='checkout(" . $row['id'] . ", \"$tier\")'>Verify</button>";
            } else {
                echo "✅";
            }
            echo "</td>";
        }
        echo "</tr>";
    }
} else {
    $colspan = ($mode === 'full') ? 5 : 4;
    echo "<tr><td colspan='$colspan' style='text-align:center'>No records found in $table</td></tr>";
}
?>