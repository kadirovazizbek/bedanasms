<?php

namespace BedanaSmsSender\Exceptions;

use Exception;

class SmsException extends Exception
{
    /**
     * @var array|null
     */
    protected $response;

    /**
     * SmsException constructor.
     *
     * @param string $message
     * @param int $code
     * @param array|null $response
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, ?array $response = null, ?\Throwable $previous = null)
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the API response that caused this exception.
     *
     * @return array|null
     */
    public function getResponse(): ?array
    {
        return $this->response;
    }

    /**
     * Create a new exception from an API response.
     *
     * @param array $response
     * @return static
     */
    public static function fromResponse(array $response): self
    {
        $message = $response['message'] ?? 'Unknown SMS API error';
        $code = isset($response['code']) ? (int) $response['code'] : 0;

        return new static($message, $code, $response);
    }
}
