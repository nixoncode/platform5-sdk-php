<?php

declare(strict_types=1);

namespace Platform5\Sdk\Service;

use Platform5\Sdk\Client;

class Email
{
    public function __construct(
        private Client $client,
    ) {
    }

    public function send(array $params): array
    {
        return $this->client->request('POST', '/v1/email/send', $params, $this->uuid()) ?? [];
    }

    private function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
