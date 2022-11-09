<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
  public const DEFAULT_CATEGORY = 'default_cat_for_admin_user';

  public function getDependencies(): array
  {
    return [
      UserFixtures::class,
    ];
  }

  public function load(ObjectManager $manager): void
  {
    $category = new Category($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
    $category->setName('Default');
    $manager->persist($category);

    $manager->flush();

    $this->addReference(self::DEFAULT_CATEGORY, $category);
  }
}
