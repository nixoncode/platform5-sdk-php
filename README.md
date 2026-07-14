# platform5/sdk

PHP SDK for the Platform5 Developer API.

## Install

```sh
composer require platform5/sdk
```

## Usage

```php
<?php

require 'vendor/autoload.php';

use Platform5\Sdk\Platform5;

$client = new Platform5('p5_live_abc123def456', 'http://localhost:8084');

// Send an SMS
$sms = $client->sms()->send([
    'to' => '+254712345678',
    'message' => 'Your appointment is confirmed for tomorrow at 10 AM.',
    'from' => 'MyBrand',
]);
echo $sms['message_id'];

// Send an email
$client->email()->send([
    'to' => 'user@example.com',
    'subject' => 'Welcome',
    'body' => 'Hello!',
    'from' => 'MyBrand',
]);

// Check message status
$status = $client->messages()->get('msg-uuid');

// Check balance
$balance = $client->account()->getBalance();
```

## Configuration

```php
new Platform5(
    apiKey: 'p5_live_abc123',
    baseUrl: 'http://localhost:8084',  // optional
);
```

## Services

| Method | Endpoint |
|--------|----------|
| `$client->sms()->send([...])` | POST /v1/sms/send |
| `$client->email()->send([...])` | POST /v1/email/send |
| `$client->messages()->get($id)` | GET /v1/messages/{id} |
| `$client->account()->getBalance()` | GET /v1/balance |
| `$client->health()` | GET /health |

## Error Handling

```php
use Platform5\Sdk\Exception\RateLimitException;
use Platform5\Sdk\Exception\Platform5Exception;

try {
    $client->sms()->send([...]);
} catch (RateLimitException $e) {
    echo "Rate limited: {$e->remaining}/{$e->limit}";
} catch (Platform5Exception $e) {
    echo "API error {$e->statusCode}: {$e->getMessage()}";
}
```

## Requirements

- PHP 8.1+
