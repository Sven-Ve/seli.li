<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AcceptableUseTest extends WebTestCase
{
  public function testAcceptableUse(): void
  {
    $client = static::createClient();
    $crawler = $client->request('GET', '/acceptable-use/');

    $this->assertResponseStatusCodeSame(200);
  }
}
