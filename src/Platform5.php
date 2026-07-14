<?php

declare(strict_types=1);

namespace Platform5\Sdk;

use Platform5\Sdk\Service\Account;
use Platform5\Sdk\Service\Email;
use Platform5\Sdk\Service\Messages;
use Platform5\Sdk\Service\Sms;

class Platform5
{
    private Client $client;
    private Sms $sms;
    private Email $email;
    private Messages $messages;
    private Account $account;

    public function __construct(string $apiKey, ?string $baseUrl = null)
    {
        $this->client = new Client($apiKey, $baseUrl);
        $this->sms = new Sms($this->client);
        $this->email = new Email($this->client);
        $this->messages = new Messages($this->client);
        $this->account = new Account($this->client);
    }

    public function sms(): Sms
    {
        return $this->sms;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function messages(): Messages
    {
        return $this->messages;
    }

    public function account(): Account
    {
        return $this->account;
    }

    public function health(): void
    {
        $this->client->request('GET', '/health');
    }
}
