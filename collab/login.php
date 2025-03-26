<?php
require 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT password FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
        echo "success";
    } else {
        echo "invalid";
    }
} else {
    echo "invalid";
}
?>
