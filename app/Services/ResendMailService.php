<?php

namespace App\Services;

use Resend\Client;
use Illuminate\Support\Facades\Log;

class ResendMailService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.resend.key');

        if (!$this->apiKey) {
            Log::error('Resend API key not configured');
            throw new \Exception('Resend API key not configured');
        }

        $this->client = new Client($this->apiKey);
    }

    /**
     * Send an email using Resend API
     *
     * @param string $to
     * @param string $subject
     * @param string $content
     * @param array $options
     * @return array|null
     */
    public function sendEmail(string $to, string $subject, string $content, array $options = []): ?array
    {
        try {
            $from = $options['from'] ?? config('mail.from.address');
            $fromName = $options['from_name'] ?? config('mail.from.name');

            $params = [
                'from' => $fromName ? "{$fromName} <{$from}>" : $from,
                'to' => $to,
                'subject' => $subject,
                'html' => $content,
            ];

            // Add CC if provided
            if (!empty($options['cc'])) {
                $params['cc'] = is_array($options['cc']) ? $options['cc'] : [$options['cc']];
            }

            // Add BCC if provided
            if (!empty($options['bcc'])) {
                $params['bcc'] = is_array($options['bcc']) ? $options['bcc'] : [$options['bcc']];
            }

            // Add reply-to if provided
            if (!empty($options['reply_to'])) {
                $params['reply_to'] = $options['reply_to'];
            }

            $result = $this->client->emails->send($params);

            Log::info('Email sent successfully via Resend', [
                'to' => $to,
                'subject' => $subject,
                'message_id' => $result->id ?? 'unknown',
            ]);

            return [
                'success' => true,
                'message_id' => $result->id ?? null,
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send email via Resend', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send a text email using Resend API
     *
     * @param string $to
     * @param string $subject
     * @param string $text
     * @param array $options
     * @return array|null
     */
    public function sendTextEmail(string $to, string $subject, string $text, array $options = []): ?array
    {
        try {
            $from = $options['from'] ?? config('mail.from.address');
            $fromName = $options['from_name'] ?? config('mail.from.name');

            $params = [
                'from' => $fromName ? "{$fromName} <{$from}>" : $from,
                'to' => $to,
                'subject' => $subject,
                'text' => $text,
            ];

            // Add CC if provided
            if (!empty($options['cc'])) {
                $params['cc'] = is_array($options['cc']) ? $options['cc'] : [$options['cc']];
            }

            // Add BCC if provided
            if (!empty($options['bcc'])) {
                $params['bcc'] = is_array($options['bcc']) ? $options['bcc'] : [$options['bcc']];
            }

            // Add reply-to if provided
            if (!empty($options['reply_to'])) {
                $params['reply_to'] = $options['reply_to'];
            }

            $result = $this->client->emails->send($params);

            Log::info('Text email sent successfully via Resend', [
                'to' => $to,
                'subject' => $subject,
                'message_id' => $result->id ?? 'unknown',
            ]);

            return [
                'success' => true,
                'message_id' => $result->id ?? null,
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send text email via Resend', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if Resend service is available
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey) && !empty($this->client);
    }
}
