<?php
$conn = new mysqli("localhost", "root", "", "qrcodegenerate");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
?>