<?php
require 'db.php';

// Check if the form is submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = trim($_POST['fullname']); // Adjusted to use 'name' instead of 'fullname'
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_type = trim($_POST['user_type']);

    // Validate password confirmation
    if ($password !== $confirm_password) {
        echo "Error: Passwords do not match.";
        exit;
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into the users table using 'name' instead of 'fullname'
    $sql = "INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo "Error: Unable to prepare statement.";
        exit;
    }

    $stmt->bind_param("ssss", $name, $email, $hashedPassword, $user_type);

    if ($stmt->execute()) {
        echo "Registration successful! Redirecting to login...";
        header("refresh:2;url=login.html"); // Redirect to login after 2 seconds
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
