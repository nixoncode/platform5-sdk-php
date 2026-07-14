<?php

require __DIR__ . '/../vendor/autoload.php';

use Platform5\Sdk\Platform5;

$client = new Platform5('p5_live_abc123def456', 'http://localhost:8084');

// Health
$client->health();

// Send SMS
$sms = $client->sms()->send([
    'to' => '+254712345678',
    'message' => 'Your appointment is confirmed.',
    'from' => 'MyBrand',
]);
echo "Sent: {$sms['message_id']} ({$sms['parts']} parts, {$sms['cost']} {$sms['currency']})\n";

// Send email
$email = $client->email()->send([
    'to' => 'user@example.com',
    'subject' => 'Welcome',
    'body' => 'Hello!',
    'from' => 'MyBrand',
]);
echo "Email: {$email['message_id']}\n";

// Check status
$status = $client->messages()->get($sms['message_id']);
echo "Status: {$status['status']}\n";

// Check balance
$balance = $client->account()->getBalance();
echo "Balance: {$balance['available_balance']} {$balance['currency']}\n";
