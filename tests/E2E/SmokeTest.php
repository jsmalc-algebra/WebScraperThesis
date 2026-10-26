<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class SmokeTest extends PantherTestCase
{


    public function testHomepageLoads(): void
    {
        $client = static::createPantherClient();
        $client->request('GET', '/');

        $this->assertSelectorExists('body');
    }
}
