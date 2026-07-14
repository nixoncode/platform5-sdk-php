<?php

declare(strict_types=1);

namespace Platform5\Sdk\Tests;

use PHPUnit\Framework\TestCase;
use Platform5\Sdk\Platform5;

class Platform5Test extends TestCase
{
    public function testCreatesClient(): void
    {
        $app = new Platform5('test-key');
        $this->assertNotNull($app->sms());
        $this->assertNotNull($app->email());
        $this->assertNotNull($app->messages());
        $this->assertNotNull($app->account());
    }
}
