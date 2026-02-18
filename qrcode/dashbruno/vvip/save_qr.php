<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qrcodegenerate";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if BOTH qrcode and price are set
if (isset($_POST['qrcode']) && isset($_POST['price'])) {
    $qr = $conn->real_escape_string($_POST['qrcode']);
    $price = $conn->real_escape_string($_POST['price']);
    
    // Inserting QR, Price, and setting the verified column to 'Pending'
    $sql = "INSERT INTO qrcodevvip (qrcodevvip, price, verified) 
            VALUES ('$qr', '$price', 'Pending')";

    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>