<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileFormTest extends WebTestCase
{
  public function testContactForm(): void
  {
    $client = static::createClient();
    $userRepository = static::getContainer()->get(UserRepository::class);
    $adminUser = $userRepository->findOneByEmail('admin@admin.test');

    // simulate $testUser being logged in
    $client->loginUser($adminUser);

    $crawler = $client->request('GET', '/profile/');

    $this->assertResponseIsSuccessful();
    $this->assertSelectorTextContains('h2', 'Edit Profile');
  }
}
