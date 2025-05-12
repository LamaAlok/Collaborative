<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, password, user_type FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashedPassword, $user_type);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_type'] = $user_type;

            // Redirect based on role
            if ($user_type === 'admin') {
                header("Location: admin.php");
                exit;
            } elseif ($user_type === 'collector') {
                header("Location: pickup-action.php");  // Your collector dashboard
                exit;
            } else {
                header("Location: dashboardB.php"); // ✅ Updated for general users
                exit;
            }
        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "Invalid email or password.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Please fill in both fields.";
}
?>
