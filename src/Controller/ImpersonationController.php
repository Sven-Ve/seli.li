<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AppConstants;
use Svc\LogBundle\Service\EventLog;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImpersonationController extends _BaseController
{
  /**
   * start/end Impersonation.
   */
  #[Route(path: '/impers/{id?null}', name: 'home_impers')]
  public function homeImpersonation(?User $user, EventLog $eventLog): Response
  {
    if (!$this->isGranted('ROLE_USER')) {
      $eventLog->log(0, AppConstants::LOG_TYPE_IMPERSONATION_ERROR, ['level' => EventLog::LEVEL_ERROR, 'message' => 'Anonym tried impersonation']);
      $this->addFlash('warning', 'This function is not allowed for you.');
    }

    $this->denyAccessUnlessGranted('ROLE_USER');

    if (!$user and $this->isGranted('IS_IMPERSONATOR')) {
      $eventLog->log($this->getUser()->getId(), AppConstants::LOG_TYPE_IMPERSONATION_END, ['level' => EventLog::LEVEL_INFO, 'message' => 'Impersonation end.']);

      return $this->redirectToRoute('home', ['_switch_user' => '_exit']);
    }

    if (!$this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
      $eventLog->log($this->getUser()->getId() ?? 0, AppConstants::LOG_TYPE_IMPERSONATION_ERROR, ['level' => EventLog::LEVEL_ERROR, 'message' => 'Tried impersonation to ' . $user->getEmail()]);
      $this->addFlash('warning', 'This function is not allowed for you.');

      return $this->redirectToRoute('home');
    }

    if ($user->getId() == $this->getUser()->getId()) {
      $eventLog->log($this->getUser()->getId(), AppConstants::LOG_TYPE_IMPERSONATION_ERROR, ['level' => EventLog::LEVEL_WARN, 'message' => 'Tried impersonation to self']);
      $this->addFlash('warning', 'You cannot switch to yourself.');

      return $this->redirectToRoute('home');
    }

    $eventLog->log($this->getUser()->getId(), AppConstants::LOG_TYPE_IMPERSONATION_START, ['level' => EventLog::LEVEL_INFO,
      'message' => 'Impersonation to ' . $user->getEmail() . ' (' . $user->getFirstName() . ' ' . $user->getLastName() . ')', ]);

    return $this->redirectToRoute('home', ['_switch_user' => $user->getUserIdentifier()]);
  }
}
