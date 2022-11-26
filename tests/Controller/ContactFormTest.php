<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactFormTest extends WebTestCase
{
  public function testContactFormEN(): void
  {
    $client = static::createClient();
    $crawler = $client->request('GET', '/svc-contactform/contact/');

    $this->assertResponseIsSuccessful();
    $this->assertSelectorTextContains('h3', 'Contact');
  }
}
