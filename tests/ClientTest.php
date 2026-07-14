<?php

declare(strict_types=1);

namespace Platform5\Sdk\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Platform5\Sdk\Client;
use Platform5\Sdk\Exception\UnauthorizedException;
use Platform5\Sdk\Exception\RateLimitException;

class ClientTest extends TestCase
{
    private function mockClient(int $status, array $body, array $headers = []): Client
    {
        $mock = new MockHandler([new Response($status, $headers, json_encode($body))]);
        $handler = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handler]);

        $client = new class('test-key', 'http://localhost:8084') extends Client {
            public function setGuzzle(GuzzleClient $guzzle): void
            {
                $ref = new \ReflectionProperty(Client::class, 'http');
                $ref->setAccessible(true);
                $ref->setValue($this, $guzzle);
            }
        };
        $client->setGuzzle($guzzle);

        return $client;
    }

    public function testHealthOk(): void
    {
        $client = $this->mockClient(200, ['success' => true, 'message' => 'OK', 'data' => null]);
        $result = $client->request('GET', '/health');
        $this->assertNull($result);
    }

    public function testUnauthorized(): void
    {
        $this->expectException(UnauthorizedException::class);
        $client = $this->mockClient(401, ['success' => false, 'message' => 'Unauthorized', 'errors' => 'bad key']);
        $client->request('GET', '/health');
    }

    public function testRateLimit(): void
    {
        $client = $this->mockClient(429, ['success' => false, 'message' => 'Rate limited'], [
            'X-RateLimit-Limit' => '50',
            'X-RateLimit-Remaining' => '0',
        ]);

        try {
            $client->request('GET', '/health');
        } catch (RateLimitException $e) {
            $this->assertEquals(50, $e->limit);
            $this->assertEquals(0, $e->remaining);
            return;
        }

        $this->fail('Expected RateLimitException');
    }
}
