<?php

declare(strict_types=1);

namespace Platform5\Sdk;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Platform5\Sdk\Exception\ForbiddenException;
use Platform5\Sdk\Exception\InsufficientBalanceException;
use Platform5\Sdk\Exception\NotFoundException;
use Platform5\Sdk\Exception\Platform5Exception;
use Platform5\Sdk\Exception\RateLimitException;
use Platform5\Sdk\Exception\UnauthorizedException;
use Platform5\Sdk\Exception\ValidationException;

class Client
{
    private const DEFAULT_BASE_URL = 'http://localhost:8084';

    private GuzzleClient $http;

    public function __construct(
        private string $apiKey,
        ?string $baseUrl = null,
    ) {
        $this->http = new GuzzleClient([
            'base_uri' => rtrim($baseUrl ?? self::DEFAULT_BASE_URL, '/') . '/',
            'headers' => [
                'Content-Type' => 'application/json',
                'X-API-Key' => $apiKey,
            ],
            'http_errors' => false,
        ]);
    }

    public function request(string $method, string $path, ?array $body = null, ?string $idempotencyKey = null): ?array
    {
        $options = [];

        if ($body !== null) {
            $options['json'] = $body;
        }

        if ($idempotencyKey !== null) {
            $options['headers']['Idempotency-Key'] = $idempotencyKey;
        }

        try {
            $response = $this->http->request($method, ltrim($path, '/'), $options);
        } catch (GuzzleException $e) {
            throw new Platform5Exception(0, 'Request failed: ' . $e->getMessage(), null, null);
        }

        $requestId = $response->getHeaderLine('X-Request-ID') ?: null;
        $statusCode = $response->getStatusCode();
        $body = json_decode((string) $response->getBody(), true) ?? [];

        if ($statusCode >= 400) {
            throw $this->toError($statusCode, $response->getHeaders(), $body, $requestId);
        }

        return $body['data'] ?? null;
    }

    private function toError(int $status, array $headers, array $body, ?string $requestId): Platform5Exception
    {
        $message = $body['message'] ?? '';
        $errors = $body['errors'] ?? null;

        if ($status === 429) {
            $limit = (int) ($headers['X-RateLimit-Limit'][0] ?? 0);
            $remaining = (int) ($headers['X-RateLimit-Remaining'][0] ?? 0);
            return new RateLimitException($status, $message, $errors, $requestId, $limit, $remaining);
        }

        return match ($status) {
            401 => new UnauthorizedException($status, $message, $errors, $requestId),
            402 => new InsufficientBalanceException($status, $message, $errors, $requestId),
            403 => new ForbiddenException($status, $message, $errors, $requestId),
            404 => new NotFoundException($status, $message, $errors, $requestId),
            422 => new ValidationException($status, $message, $errors, $requestId),
            default => new Platform5Exception($status, $message, $errors, $requestId),
        };
    }
}
