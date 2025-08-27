<?php

namespace BedanaSmsSender;

/**
 * Interface for SMS client implementations
 */
interface SmsClientInterface
{
    /**
     * Send a single SMS message
     *
     * @param array $message
     * @return array
     */
    public function send(array $message): array;

    /**
     * Send multiple SMS messages
     *
     * @param array $messages
     * @return array
     */
    public function sendBatch(array $messages): array;

    /**
     * Get the status of an SMS message
     *
     * @param string $messageId
     * @return array
     */
    public function getStatus(string $messageId): array;
}
