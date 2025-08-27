# Bedana SMS Sender

A framework-agnostic PHP library for sending SMS messages via API.

## Installation

```bash
composer require bedana/sms-sender
```

## Usage

The library provides two client implementations:

1. `SmsClient` - Uses Guzzle HTTP client (recommended)
2. `SimpleSmsClient` - Uses PHP's built-in `file_get_contents` for HTTP requests (no dependencies)

### Using SmsClient (with Guzzle)

```php
use BedanaSmsSender\SmsClient;

// Create a new SMS client
$client = new SmsClient('YOUR_API_KEY', 'https://bedana.uz');

// Send a single SMS
$response = $client->send([
    'phone' => '998123456789',
    'text' => 'Hello from PHP SMS Sender!'
]);

// Send multiple SMS messages
$response = $client->sendBatch([
    [
        'phone' => '998123456789',
        'text' => 'Message 1',
        'operator' => 'uzmobile',
        'prefix' => '998'
    ],
    [
        'phone' => '998987654321',
        'text' => 'Message 2',
        'operator' => 'ucell',
        'prefix' => '998'
    ]
]);
```

### Using SimpleSmsClient (no dependencies)

```php
use BedanaSmsSender\SimpleSmsClient;

// Create a new Simple SMS client (no Guzzle dependency)
$client = new SimpleSmsClient('YOUR_API_KEY', 'https://api.example.com');

// The rest of the API is identical
$response = $client->send([
    'phone' => '998123456789',
    'text' => 'Hello from PHP SMS Sender!',
]);
```

## Message Status

To check the status of a message:

```php
$status = $client->getStatus('message_id_here');
```

## Laravel Integration

If you're using Laravel, you can use our Laravel integration:

```php
// In your service provider
public function register()
{
    $this->app->singleton(SmsClient::class, function ($app) {
        return new SmsClient(
            config('services.sms.api_key'),
            config('services.sms.base_url')
        );
    });
}

// In your controller
public function sendSms(SmsService $smsService)
{
    $response = $smsService->sendMessages([
        [
            'phone' => '998123456789',
            'text' => 'Hello from Laravel!',
        ]
    ]);
    
    return $response;
}
```

## Exception Handling

```php
use BedanaSmsSender\SmsClient;
use BedanaSmsSender\Exceptions\SmsException;

$client = new SmsClient('YOUR_API_KEY');

try {
    $response = $client->send([
        'phone' => '998123456789',
        'text' => 'Hello World!',
    ]);
} catch (SmsException $e) {
    // Handle API-specific errors
    echo 'SMS Error: ' . $e->getMessage();
} catch (\Exception $e) {
    // Handle other exceptions
    echo 'Error: ' . $e->getMessage();
}
```

## License

MIT
