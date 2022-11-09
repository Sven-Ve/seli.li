<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LinkTest extends WebTestCase
{
  public function testRedirectListAnonym(): void
  {
    $client = static::createClient();

    $crawler = $client->request('GET', '/manage/link/');
    $this->assertResponseRedirects('/login');
  }

  public function testRedirectListAsAdmin(): void
  {
    $client = static::createClient();
    $userRepository = static::getContainer()->get(UserRepository::class);
    $adminUser = $userRepository->findOneByEmail('admin@admin.test');

    // simulate $testUser being logged in
    $client->loginUser($adminUser);

    $crawler = $client->request('GET', '/manage/link/');
    $this->assertResponseIsSuccessful();
  }
}
