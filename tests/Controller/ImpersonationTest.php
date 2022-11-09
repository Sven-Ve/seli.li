<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ImpersonationTest extends WebTestCase
{
  public function testImpersonation(): void
  {
    $client = static::createClient();
    $crawler = $client->request('GET', '/impers/');

    $this->assertResponseStatusCodeSame(301);
  }
}
