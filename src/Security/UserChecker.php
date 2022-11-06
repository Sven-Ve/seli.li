<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
  public function __construct(private readonly Security $security)
  {
  }

  public function checkPreAuth(UserInterface $user): void
  {
    return;
  }

  public function checkPostAuth(UserInterface $user): void
  {
    $token = $this->security->getToken();

    // is Impersonation active? then allow blocked users to login
    if ($token and $token->getUser() and ($token->getUser()->isVerified() and !$token->getUser()->isBlocked())) {
      return;
    }

    if (!$user instanceof AppUser) {
      return;
    }

    if (!$user->isVerified()) {
      throw new CustomUserMessageAuthenticationException('Your account is not verified, please click on the activation link in email.');
    }

    if ($user->isBlocked()) {
      $errorMsg = 'Your account is blocked.';
      if ($user->getBlockReason()) {
        $errorMsg .= ' (reason: ' . $user->getBlockReason() . ')';
      }
      throw new CustomUserMessageAuthenticationException($errorMsg);
    }
  }
}
