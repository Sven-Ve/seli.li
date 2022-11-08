<?php

namespace App\Security;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CategoryVoter extends Voter
{
  // these strings are just invented: you can use anything
  final public const VIEW = 'view';
  final public const EDIT = 'edit';

  public function supportsType(string $subjectType): bool
  {
    // you can't use a simple BlogPost::class === $subjectType comparison
    // here because the given subject type could be the proxy class used
    // by Doctrine when creating the entity object
    return is_a($subjectType, Category::class, true);
  }

  protected function supports(string $attribute, $subject): bool
  {
    // if the attribute isn't one we support, return false
    if (!in_array($attribute, [self::VIEW, self::EDIT])) {
      return false;
    }

    // only vote on `Category` objects
    if (!$subject instanceof Category) {
      return false;
    }

    return true;
  }

  protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
  {
    $user = $token->getUser();

    if (!$user instanceof User) {
      // the user must be logged in; if not, deny access
      return false;
    }

    // you know $subject is a Category object, thanks to `supports()`
    /** @var Category $category */
    $category = $subject;

    return match ($attribute) {
      self::VIEW => $this->canView($category, $user),
      self::EDIT => $this->canEdit($category, $user),
      default => throw new \LogicException('This code should not be reached!'),
    };
  }

  private function canView(Category $category, User $user): bool
  {
    // if they can edit, they can view
    if ($this->canEdit($category, $user)) {
      return true;
    }

    return false;
  }

  private function canEdit(Category $category, User $user): bool
  {
    return $user === $category->getUser();
  }
}
