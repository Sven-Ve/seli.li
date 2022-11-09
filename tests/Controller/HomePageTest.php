<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
  public function testHomepage(): void
  {
    $client = static::createClient();
    $crawler = $client->request('GET', '/');

    $this->assertResponseIsSuccessful();
  }
}
