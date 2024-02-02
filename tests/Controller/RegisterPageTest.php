<?php

namespace App\Tests\Controller;

use App\Service\AppConstants;
use Svc\ParamBundle\Repository\ParamsRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterPageTest extends WebTestCase
{
  private ParamsRepository|null $paramRep;

  private KernelBrowser $client;

  public function setUp(): void
  {
    $this->client = static::createClient();

    $container = static::getContainer();
    // (3) run some service & test the result
    $this->paramRep = $container->get(ParamsRepository::class);
    $this->paramRep->setBool(AppConstants::PARAM_REGISTER_ENABLED, true, 'Is registration enabled?');
  }

  public function testRegisterFormEN(): void
  {
    $crawler = $this->client->request('GET', '/register');

    $this->assertResponseIsSuccessful();
    $this->assertSelectorTextContains('h1', 'Register a new user');
  }

  public function testRegisterFormDisabled(): void
  {
    $this->paramRep->setBool(AppConstants::PARAM_REGISTER_ENABLED, false);

    $crawler = $this->client->request('GET', '/register');
    $this->assertResponseRedirects();
  }
}
