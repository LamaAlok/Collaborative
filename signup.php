<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']); // ✅ phone field added
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_type = trim($_POST['user_type']);

    // Validate password confirmation
    if ($password !== $confirm_password) {
        echo "Error: Passwords do not match.";
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Insert into users table with phone included
    $sql = "INSERT INTO users (name, email, phone, password, user_type) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error: Unable to prepare statement.";
        exit;
    }

    $stmt->bind_param("sssss", $name, $email, $phone, $hashedPassword, $user_type);

    if ($stmt->execute()) {
        echo "Registration successful! Redirecting to login...";
        header("refresh:2;url=login.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
