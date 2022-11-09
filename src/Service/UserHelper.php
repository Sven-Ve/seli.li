<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\SuperAdminDeleteForbiddenException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Svc\LogBundle\Repository\SvcLogRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;

class UserHelper
{
  public function __construct(
    private readonly EntityManagerInterface $entityManager,
    private readonly Security $security,
    private readonly SvcLogRepository $svcLogRep,
    private readonly RequestStack $requestStack)
  {
  }

  /**
   * @throws SuperAdminDeleteForbiddenException
   */
  public function userDeleteAllowed(User $user, bool $runAsAdmin = false): bool
  {
    // you can only delete yourself (started from profile, not from admin page
    if (!$runAsAdmin and $this->security->getUser()->getId() != $user->getId()) {
      return false;
    }

    // super admin are not allowed to delete
    if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
      throw new SuperAdminDeleteForbiddenException();
    }

    return true;
  }

  public function userDeleteWithAllData(User $user): bool
  {
    try {
      foreach ($user->getLinks() as $link) {
        // TODO: Constants richtig stellen, Aufruf in UserDelete und ProfileDelete aufnehmen
        $this->svcLogRep->batchDelete($link->getId(), AppConstants::LOG_TYPE_LINK_CREATED);
        $this->svcLogRep->batchDelete($link->getId(), AppConstants::LOG_TYPE_LINK_CHANGED);
        $this->svcLogRep->batchDelete($link->getId(), AppConstants::LOG_TYPE_LINK_CALLED);
        $this->svcLogRep->batchDelete($link->getId(), AppConstants::LOG_TYPE_LINK_DELETED);
      }
      $this->svcLogRep->batchDelete(null, null, $user->getId());

      foreach (AppConstants::LOG_ACCOUNT_OPERATIONS as $sourceType) {
        $this->svcLogRep->batchDelete($user->getId(), $sourceType);
      }
    } catch (Exception) {
    }

    $session = $this->requestStack->getSession();
    $session = new Session();
    $session->invalidate();

    $this->entityManager->remove($user);
    $this->entityManager->flush();

    return true;
  }
}
