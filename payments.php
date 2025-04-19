<?php
require 'db.php';
include 'nav.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $method = $_POST['method'] ?? '';
    $amount = 555;

    $cardName = '';
    $cardNumber = '';

    if ($method === 'Credit/Debit') {
        $cardName = $_POST['card_name'] ?? '';
        $cardNumber = $_POST['card_number'] ?? '';
    }

    if (!empty($fullName) && !empty($phone) && !empty($method)) {
        $now = date('Y-m-d H:i:s');
        $validUntil = date('Y-m-d H:i:s', strtotime('+1 month', strtotime($now)));

        $stmt = $conn->prepare("INSERT INTO payments (full_name, phone_number, card_name, card_number, method, amount, payment_time, expires_at, status)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Completed')");
        $stmt->bind_param("sssssdss", $fullName, $phone, $cardName, $cardNumber, $method, $amount, $now, $validUntil);

        if ($stmt->execute()) {
            echo "<script>alert('Payment successful! Now you can request a pickup.'); window.location.href='request.php';</script>";
        } else {
            echo "Payment failed. Try again.";
        }
        $stmt->close();
    } else {
        echo "Missing required fields.";
    }
}
?>
