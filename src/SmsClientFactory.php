<?php

namespace BedanaSmsSender;

/**
 * Factory class for creating SMS client instances
 */
class SmsClientFactory
{
    /**
     * Create an SMS client instance
     *
     * @param string $apiKey
     * @param string $baseUrl
     * @param bool $useSimpleClient If true, uses SimpleSmsClient instead of SmsClient
     * @return SmsClientInterface
     */
    public static function create(string $apiKey, string $baseUrl = 'https://api.example.com', bool $useSimpleClient = false): SmsClientInterface
    {
        if ($useSimpleClient) {
            return new SimpleSmsClient($apiKey, $baseUrl);
        }

        return new SmsClient($apiKey, $baseUrl);
    }

    /**
     * Create an SMS client instance that doesn't depend on Guzzle
     *
     * @param string $apiKey
     * @param string $baseUrl
     * @return SimpleSmsClient
     */
    public static function createSimpleClient(string $apiKey, string $baseUrl = 'https://api.example.com'): SimpleSmsClient
    {
        return new SimpleSmsClient($apiKey, $baseUrl);
    }

    /**
     * Create an SMS client instance that uses Guzzle
     *
     * @param string $apiKey
     * @param string $baseUrl
     * @return SmsClient
     */
    public static function createGuzzleClient(string $apiKey, string $baseUrl = 'https://api.example.com'): SmsClient
    {
        return new SmsClient($apiKey, $baseUrl);
    }
}
