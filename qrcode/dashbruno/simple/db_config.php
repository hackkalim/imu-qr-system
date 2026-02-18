<?php
$server = "sql204.infinityfree.com";
$username = "if0_41188377";
$password = "jrP3XVBEjDQBq8S";
$dbname = "if0_41188377_qrcodegenerate"; // Change this to your actual DB name

$conn = new mysqli($server, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
?>