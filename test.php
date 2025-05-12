<?php
require 'C:/xamppB/htdocs/collab/vendor/autoload.php'; // Adjust the path as needed

use ClickSend\Api\SMSApi;
use ClickSend\Configuration;
use ClickSend\ApiException;
use GuzzleHttp\Client;
use ClickSend\Model\SmsMessage;
use ClickSend\Model\SmsMessageCollection;

// ✅ Your ClickSend login credentials
$username = 'sthnatasha111@gmail.com'; // This should be your ClickSend **username**
$apiKey   = '36524EA9-0874-BDD8-DD9D-CE8C4DCF46AA';  // This should be your **API key**

// Setup client and config
$client = new Client();
$config = Configuration::getDefaultConfiguration()
    ->setUsername($username)
    ->setPassword($apiKey);

$apiInstance = new SMSApi($client, $config);

// 🔔 Define recipient and message
$phoneNumber = '+9779810027291';  // Use international format with +
$messageContent = '🚛 New pickup request received! Please check your dashboard.';

// Create the SMS message
$message = new SmsMessage([
    'source' => 'php',
    'from' => 'BinBuddy',  // Sender name (must be approved for some countries)
    'body' => $messageContent,
    'to' => $phoneNumber
]);

$smsRequest = new SmsMessageCollection(['messages' => [$message]]);

// Send SMS
try {
    $apiInstance->smsSendPost($smsRequest);
    // Optional: comment out echo in live use
    // echo "✅ SMS sent successfully!";
} catch (ApiException $e) {
    error_log("❌ Error sending SMS: " . $e->getMessage()); // log instead of echoing
}
?>
