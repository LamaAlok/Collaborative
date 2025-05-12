<?php
// Get JSON input from frontend
$data = json_decode(file_get_contents("php://input"));

// Replace with your actual Khalti secret key
$secretKey = "5aa8a01e5fac43c6be6ba3960c6fccac";

// Prepare POST fields
$args = http_build_query(array(
    'token' => $data->token,
    'amount' => $data->amount
));

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://khalti.com/api/v2/payment/verify/"); // Khalti API URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $args);

// Set headers
$headers = [
    "Authorization: Key $5aa8a01e5fac43c6be6ba3960c6fccac" // Use your secret key here
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute request
$response = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Return response
http_response_code($status_code); // Set the HTTP status code
header('Content-Type: application/json'); // Set content type
echo $response; // Output the response from Khalti API
?>
