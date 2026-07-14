<?php

declare(strict_types=1);

namespace Platform5\Sdk\Exception;

class RateLimitException extends Platform5Exception
{
    public function __construct(
        int $statusCode,
        string $message,
        mixed $errors = null,
        ?string $requestId = null,
        public readonly int $limit = 0,
        public readonly int $remaining = 0,
    ) {
        parent::__construct($statusCode, $message, $errors, $requestId);
    }
}
