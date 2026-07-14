<?php

declare(strict_types=1);

namespace Platform5\Sdk\Service;

use Platform5\Sdk\Client;

class Account
{
    public function __construct(
        private Client $client,
    ) {
    }

    public function getBalance(): array
    {
        return $this->client->request('GET', '/v1/balance') ?? [];
    }
}
