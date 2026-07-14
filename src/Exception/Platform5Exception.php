<?php

declare(strict_types=1);

namespace Platform5\Sdk\Exception;

class Platform5Exception extends \RuntimeException
{
    public function __construct(
        public readonly int $statusCode,
        string $message,
        public readonly mixed $errors = null,
        public readonly ?string $requestId = null,
    ) {
        parent::__construct($message, $statusCode);
    }
}
