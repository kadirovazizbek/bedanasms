<?php

namespace BedanaSmsSender\Http;

/**
 * A simple HTTP client class that can be used instead of Guzzle
 * This is helpful for environments where Guzzle is not available
 */
class SimpleHttpClient
{
    /**
     * @var array
     */
    private $defaultHeaders;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * SimpleHttpClient constructor.
     *
     * @param string $baseUrl
     * @param array $defaultHeaders
     */
    public function __construct(string $baseUrl, array $defaultHeaders = [])
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->defaultHeaders = $defaultHeaders;
    }

    /**
     * Send a GET request
     *
     * @param string $url
     * @param array $headers
     * @return array
     * @throws \Exception
     */
    public function get(string $url, array $headers = []): array
    {
        return $this->request('GET', $url, null, $headers);
    }

    /**
     * Send a POST request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return array
     * @throws \Exception
     */
    public function post(string $url, array $data = [], array $headers = []): array
    {
        return $this->request('POST', $url, $data, $headers);
    }

    /**
     * Send an HTTP request
     *
     * @param string $method
     * @param string $url
     * @param array|null $data
     * @param array $headers
     * @return array
     * @throws \Exception
     */
    private function request(string $method, string $url, ?array $data = null, array $headers = []): array
    {
        $fullUrl = $this->baseUrl . '/' . ltrim($url, '/');

        $options = [
            'http' => [
                'method' => $method,
                'header' => $this->buildHeaders($headers),
                'ignore_errors' => true
            ]
        ];

        if ($data !== null && $method === 'POST') {
            $options['http']['content'] = json_encode($data);
        }

        $context = stream_context_create($options);
        $result = @file_get_contents($fullUrl, false, $context);

        if ($result === false) {
            throw new \Exception("HTTP request failed: $fullUrl");
        }

        // Get response status code
        $statusCode = $this->getStatusCode($http_response_header);

        // Decode the JSON response
        $response = json_decode($result, true);

        if ($statusCode >= 400) {
            throw new \Exception("HTTP error: " . ($response['message'] ?? 'Unknown error'), $statusCode);
        }

        return $response;
    }

    /**
     * Build HTTP headers string
     *
     * @param array $headers
     * @return string
     */
    private function buildHeaders(array $headers): string
    {
        $allHeaders = array_merge($this->defaultHeaders, $headers);
        $headerLines = [];

        foreach ($allHeaders as $name => $value) {
            $headerLines[] = "$name: $value";
        }

        return implode("\r\n", $headerLines);
    }

    /**
     * Extract status code from response headers
     *
     * @param array $headers
     * @return int
     */
    private function getStatusCode(array $headers): int
    {
        if (preg_match('/^HTTP\/\d\.\d\s+(\d+)/', $headers[0], $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }
}
