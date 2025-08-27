<?php

// Example usage of the PHP SMS Sender package

require_once __DIR__ . '/vendor/autoload.php';

use BedanaSmsSender\SmsClient;
use BedanaSmsSender\SimpleSmsClient;
use BedanaSmsSender\SmsClientFactory;

// Method 1: Create client directly
$client = new SmsClient('your_api_key_here', 'https://your-sms-api.example.com');

// Method 2: Create a simple client with no external dependencies
// $client = new SimpleSmsClient('your_api_key_here', 'https://your-sms-api.example.com');

// Method 3: Use the factory (recommended)
// $client = SmsClientFactory::create('your_api_key_here', 'https://your-sms-api.example.com');
// $client = SmsClientFactory::createSimpleClient('your_api_key_here', 'https://your-sms-api.example.com');
// $client = SmsClientFactory::createGuzzleClient('your_api_key_here', 'https://your-sms-api.example.com');

try {
    // Example 1: Send a single SMS
    $response = $client->send([
        'phone' => '998123456789',
        'text' => 'Hello from PHP SMS Sender!',
        'operator' => 'uzmobile',
        'prefix' => '998'
    ]);

    echo "Single SMS response:\n";
    print_r($response);

    // Example 2: Send multiple SMS messages
    $batchResponse = $client->sendBatch([
        [
            'phone' => '998123456789',
            'text' => 'First message',
            'operator' => 'uzmobile',
            'prefix' => '998'
        ],
        [
            'phone' => '998987654321',
            'text' => 'Second message',
            'operator' => 'ucell',
            'prefix' => '998'
        ]
    ]);

    echo "\nBatch SMS response:\n";
    print_r($batchResponse);

    // Example 3: Check message status
    // Assuming you have a message ID from a previous response
    if (!empty($response['messages'][0]['id'])) {
        $messageId = $response['messages'][0]['id'];
        $status = $client->getStatus($messageId);

        echo "\nMessage status:\n";
        print_r($status);
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
