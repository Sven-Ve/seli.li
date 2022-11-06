<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends _BaseController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
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
  public function acceptableUse(string $_locale): Response
  {
      return $this->render('home/acceptable_use_en.html.twig');
  }
}
