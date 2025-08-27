<?php

namespace BedanaSmsSender;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;
use InvalidArgumentException;
use BedanaSmsSender\Exceptions\SmsException;

class SmsClient implements SmsClientInterface
{
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * SmsClient constructor.
     *
     * @param string $apiKey
     * @param string $baseUrl
     */
    public function __construct(string $apiKey, string $baseUrl = 'https://api.example.com')
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);
    }

    /**
     * Send a single SMS message
     *
     * @param array $message
     * @return array
     * @throws \Exception
     */
    public function send(array $message): array
    {
        $this->validateMessage($message);

        return $this->sendRequest('/api/v1/sms/send', [
            'messages' => [$message]
        ]);
    }

    /**
     * Send multiple SMS messages
     *
     * @param array $messages
     * @return array
     * @throws \Exception
     */
    public function sendBatch(array $messages): array
    {
        foreach ($messages as $message) {
            $this->validateMessage($message);
        }

        return $this->sendRequest('/api/v1/sms/send', [
            'messages' => $messages
        ]);
    }

    /**
     * Get the status of an SMS message
     *
     * @param string $messageId
     * @return array
     * @throws \Exception
     */
    public function getStatus(string $messageId): array
    {
        try {
            $response = $this->httpClient->get("/api/v1/sms/status/{$messageId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            if ($e->getCode() == 404) {
                return [
                    'status' => 'error',
                    'message' => 'Message not found'
                ];
            }
            throw new SmsException('Failed to get message status: ' . $e->getMessage(), $e->getCode(), null, $e);
        }
    }

    /**
     * Send an HTTP request to the API
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws \Exception
     */
    private function sendRequest(string $endpoint, array $data): array
    {
        try {
            $response = $this->httpClient->post($endpoint, [
                'json' => $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new SmsException('Failed to send SMS: ' . $e->getMessage(), $e->getCode(), null, $e);
        }
    }

    /**
     * Validate the message data
     *
     * @param array $message
     * @return bool
     * @throws \InvalidArgumentException
     */
    private function validateMessage(array $message): bool
    {
        $requiredFields = ['phone', 'text', 'operator', 'prefix'];

        foreach ($requiredFields as $field) {
            if (!isset($message[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        // Validate phone number format (Uzbekistan format from controller)
        if (!preg_match('/^998\d{9}$/', $message['phone'])) {
            throw new \InvalidArgumentException('Invalid phone number format. Must be 998 followed by 9 digits.');
        }

        return true;
    }
}
