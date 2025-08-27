<?php

namespace BedanaSmsSender\Integrations\Laravel;

use BedanaSmsSender\SmsClient;
use BedanaSmsSender\Exceptions\SmsException;

class SmsService
{
    /**
     * @var SmsClient
     */
    protected $smsClient;

    /**
     * SmsService constructor.
     *
     * @param SmsClient $smsClient
     */
    public function __construct(SmsClient $smsClient)
    {
        $this->smsClient = $smsClient;
    }

    /**
     * Send SMS messages through the API.
     *
     * @param array $messages Array of message data
     * @param object|null $user The user sending the messages (optional)
     * @return array
     * @throws SmsException
     */
    public function sendMessages(array $messages, $user = null): array
    {
        try {
            $response = $this->smsClient->sendBatch($messages);
            return $response['messages'] ?? [];
        } catch (SmsException $e) {
            // You might want to log this or handle it differently
            throw $e;
        }
    }

    /**
     * Check the status of a message.
     *
     * @param string $messageId
     * @return array
     * @throws SmsException
     */
    public function checkStatus(string $messageId): array
    {
        return $this->smsClient->getStatus($messageId);
    }
}
