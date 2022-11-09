<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
  public const ADMIN_USER_REFERENCE = 'admin-user';

  public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
  {
  }

  public function load(ObjectManager $manager): void
  {
    $user = new User();
    $user->setEmail('admin@admin.test');
    $user->setFirstName('first admin');
    $user->setLastName('last admin');
    $user->setCountry('CH');
    $user->setRoles(['ROLE_ADMIN']);

    $user->setPassword($this->userPasswordHasher->hashPassword($user, 'test@test.test'));

    $manager->persist($user);
    $manager->flush();

    $this->addReference(self::ADMIN_USER_REFERENCE, $user);
  }
}
