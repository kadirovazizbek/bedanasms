<?php

namespace BedanaSmsSender\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use BedanaSmsSender\SmsClient;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SmsClientTest extends TestCase
{
    protected $smsClient;
    protected $mockHandler;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);

        $httpClient = new Client(['handler' => $handlerStack]);

        // Use reflection to set the protected httpClient property
        $this->smsClient = new SmsClient('test_api_key', 'https://api.example.com');
        $reflection = new ReflectionClass($this->smsClient);
        $httpClientProperty = $reflection->getProperty('httpClient');
        $httpClientProperty->setAccessible(true);
        $httpClientProperty->setValue($this->smsClient, $httpClient);
    }

    public function testSendSingleMessage()
    {
        // Mock a successful response
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'status' => 'ok',
                'messages' => [
                    [
                        'id' => 'msg123',
                        'status' => 'sent',
                        'phone' => '998123456789'
                    ]
                ]
            ]))
        );

        $response = $this->smsClient->send([
            'phone' => '998123456789',
            'text' => 'Test message',
            'operator' => 'test',
            'prefix' => '998'
        ]);

        $this->assertEquals('ok', $response['status']);
        $this->assertCount(1, $response['messages']);
        $this->assertEquals('msg123', $response['messages'][0]['id']);
    }

    public function testSendBatchMessages()
    {
        // Mock a successful response
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'status' => 'ok',
                'messages' => [
                    [
                        'id' => 'msg123',
                        'status' => 'sent',
                        'phone' => '998123456789'
                    ],
                    [
                        'id' => 'msg124',
                        'status' => 'sent',
                        'phone' => '998987654321'
                    ]
                ]
            ]))
        );

        $response = $this->smsClient->sendBatch([
            [
                'phone' => '998123456789',
                'text' => 'Test message 1',
                'operator' => 'test',
                'prefix' => '998'
            ],
            [
                'phone' => '998987654321',
                'text' => 'Test message 2',
                'operator' => 'test',
                'prefix' => '998'
            ]
        ]);

        $this->assertEquals('ok', $response['status']);
        $this->assertCount(2, $response['messages']);
    }

    public function testGetMessageStatus()
    {
        // Mock a successful response
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'id' => 'msg123',
                'status' => 'delivered',
                'phone' => '998123456789',
                'delivered_at' => '2023-07-23 12:34:56'
            ]))
        );

        $status = $this->smsClient->getStatus('msg123');

        $this->assertEquals('delivered', $status['status']);
        $this->assertEquals('msg123', $status['id']);
    }

    public function testGetStatusNotFound()
    {
        // Mock a 404 response
        $this->mockHandler->append(
            new Response(404)
        );

        $status = $this->smsClient->getStatus('not_found');

        $this->assertEquals('error', $status['status']);
        $this->assertEquals('Message not found', $status['message']);
    }
}
