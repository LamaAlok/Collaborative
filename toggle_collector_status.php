<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $_SESSION['collector_status'] = $_POST['status'];
    echo "Status updated.";
} else {
    echo "Invalid request.";
}
