<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\LinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * helper class for handle categories.
 */
class CategoryHelper
{
  public function __construct(private readonly CategoryRepository $categoryRepository, private readonly EntityManagerInterface $entityManager, private readonly LinkRepository $linkRep)
  {
  }

  /**
   * return the user's default category and create one, if not exists.
   */
  public function getDefaultCategory(UserInterface $user): Category
  {
    $defCat = $this->categoryRepository->getDefaultCategory($user);
    if (!$defCat) {
      $defCat = $this->createDefaultCategory($user);
    }

    return $defCat;
  }

  /**
   * create a new category entry for the user.
   */
  private function createDefaultCategory(UserInterface $user): Category
  {
    $defCat = new Category($user);
    $defCat->setName('Default');
    $defCat->setDefaultCategory(true);

    $this->entityManager->persist($defCat);
    $this->entityManager->flush();

    return $defCat;
  }

  /**
   * set a new default category, reset the old one.
   */
  public function setDefault(Category $newDefault, UserInterface $user): bool
  {
    $oldDefault = $this->getDefaultCategory($user);

    if ($oldDefault->getId() == $newDefault->getId()) {
      return true;
    }

    $oldDefault->setDefaultCategory(false);
    $newDefault->setDefaultCategory(true);

    $this->entityManager->persist($oldDefault);
    $this->entityManager->persist($newDefault);
    $this->entityManager->flush();

    return true;
  }

  /**
   * (for deleting a category) reset redirects to default category for a specific category.
   */
  public function resetToDefaultCategory(Category $oldCategory, UserInterface $user): void
  {
    $defaultCategory = $this->getDefaultCategory($user);
    foreach ($this->linkRep->findBy(['category' => $oldCategory]) as $redirect) {
      $redirect->setCategory($defaultCategory);
    }
    $this->entityManager->flush();
  }
}
