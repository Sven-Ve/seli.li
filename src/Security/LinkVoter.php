<?php

namespace App\Security;

use App\Entity\Link;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class LinkVoter extends Voter
{
  // these strings are just invented: you can use anything
  final public const VIEW = 'view';
  final public const EDIT = 'edit';

  public function supportsType(string $subjectType): bool
  {
    // you can't use a simple BlogPost::class === $subjectType comparison
    // here because the given subject type could be the proxy class used
    // by Doctrine when creating the entity object
    return is_a($subjectType, Link::class, true);
  }

  protected function supports(string $attribute, $subject): bool
  {
    // if the attribute isn't one we support, return false
    if (!in_array($attribute, [self::VIEW, self::EDIT])) {
      return false;
    }

    // only vote on `Redirects` objects
    if (!$subject instanceof Link) {
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

    // you know $subject is a Redirects object, thanks to `supports()`
    /** @var Link $link */
    $link = $subject;

    return match ($attribute) {
      self::VIEW => $this->canView($link, $user),
      self::EDIT => $this->canEdit($link, $user),
      default => throw new \LogicException('This code should not be reached!'),
    };
  }

  private function canView(Link $link, User $user): bool
  {
    // if they can edit, they can view
    if ($this->canEdit($link, $user)) {
      return true;
    }

    return false;
  }

  private function canEdit(Link $link, User $user): bool
  {
    return $user === $link->getUser();
  }
}
