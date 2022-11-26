<?php

namespace App\Controller;

use App\Service\AppConstants;
use Svc\LogBundle\Service\EventLog;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends _BaseController
{
  #[Route('/', name: 'home')]
  public function index(EventLog $eventLog): Response
  {
    /*
        \Sentry\withScope(function (\Sentry\State\Scope $scope): void {
          $scope->setUser(['username' => 'max@moritz.de']);
          $scope->setTag("svc_sender", "svc_log");

          $scope->setContext('svc_log', [
            'object_id' => 1,
            'object_type' => 2,
          ]);
          captureMessage("Hallo2",Severity::fatal());
        });


    dd("halt");
    */

    if (!$this->isGranted('ROLE_USER')) {
      $eventLog->log(0, AppConstants::LOG_TYPE_ANONHOME, ['level' => EventLog::LEVEL_INFO]);

      return $this->render('home/index.html.twig');
    }

    return $this->redirectToRoute('link_list_index');
  }

  /**
   * display the "Datenschutzbedingungen".
   */
  #[Route(path: '/datenschutz', name: 'dataprotection')]
  public function dataProtection(): Response
  {
    return $this->render('home/datenschutz.html.twig');
  }

  /**
   * Display Impressum.
   */
  #[Route(path: '/impressum', name: 'impressum')]
  public function impressum(): Response
  {
    return $this->render('home/impressum.html.twig');
  }

  /**
   * Richtlinie zur akzeptablen Nutzung.
   */
  #[Route(path: '/acceptable-use/', name: 'acceptableUse')]
  public function acceptableUse(): Response
  {
    return $this->render('home/acceptable_use_en.html.twig');
  }
}
