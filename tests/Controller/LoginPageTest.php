<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginPageTest extends WebTestCase
{
  public function testLoginEN(): void
  {
    $client = static::createClient();
    $crawler = $client->request('GET', '/login');

    $this->assertResponseIsSuccessful();
    $this->assertSelectorTextContains('h1', 'Please sign in');
  }

}
