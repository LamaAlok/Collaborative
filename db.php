<?php
$host = 'localhost';
$db = 'waste_management';
$user = 'root';
$pass = ''; // ← add your password if you set one

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
