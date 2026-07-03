<?php

namespace App\Services\Twilio;

/**
 * Result of a WhatsApp send attempt, carrying enough detail for the caller to
 * decide whether an SMS fallback is worth attempting instead of always trying it.
 */
class SendOutcome
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $sid = null,
        public readonly ?int $errorCode = null,
        public readonly ?string $errorMessage = null,
        public readonly bool $fallbackToSmsRecommended = true,
    ) {
    }

    public static function ok(string $sid): self
    {
        return new self(success: true, sid: $sid);
    }

    public static function failed(?int $errorCode, ?string $errorMessage, bool $fallbackToSmsRecommended = true): self
    {
        return new self(
            success: false,
            errorCode: $errorCode,
            errorMessage: $errorMessage,
            fallbackToSmsRecommended: $fallbackToSmsRecommended,
        );
    }
}
