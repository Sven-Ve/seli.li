<?php

namespace App\DataFixtures;

use App\Entity\Link;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LinkFixtures extends Fixture implements DependentFixtureInterface
{
  public function getDependencies(): array
  {
    return [
      UserFixtures::class, CategoryFixtures::class,
    ];
  }

  public function load(ObjectManager $manager): void
  {
    $redirect = new Link();
    $redirect->setName('test');
    $redirect->setUrl('https://www.test.test');
    $redirect->setUser($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
    $redirect->setCategory($this->getReference(CategoryFixtures::DEFAULT_CATEGORY));

    $manager->persist($redirect);
    $manager->flush();
  }
}
